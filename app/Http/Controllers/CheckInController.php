<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckInController extends Controller
{
    /**
     * GET /checkin (checkin.index)
     * Staff Check-In Hub: active registrations, manual/Staff ID check-in,
     * HR venue scanner launchers, and verified attendance history.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $registrations = Registration::with(['event', 'waiver', 'checkIn'])
            ->where('user_id', $user->id)
            ->get()
            ->filter(fn (Registration $registration): bool => $registration->event !== null)
            ->sortByDesc(fn (Registration $registration) => $registration->event->starts_at)
            ->values();

        $activeRegistrations = $registrations
            ->filter(fn (Registration $registration): bool => $registration->checkIn === null)
            ->values();

        $pastCheckIns = $registrations
            ->filter(fn (Registration $registration): bool => $registration->checkIn !== null)
            ->values();

        $upcomingEvents = Event::where('starts_at', '>=', now()->startOfDay())
            ->orderBy('starts_at', 'asc')
            ->get();

        return view('checkin.index', [
            'user'                => $user,
            'activeRegistrations' => $activeRegistrations,
            'pastCheckIns'        => $pastCheckIns,
            'upcomingEvents'      => $upcomingEvents,
            'totalCheckIns'       => $pastCheckIns->count(),
            'pointsBalance'       => (int) ($user->points_balance ?? 0),
        ]);
    }

    /**
     * GET /checkin/event/{event} (checkin.scan)
     * Venue / Event Arrival Check-In screen for a specific event.
     */
    public function scan(Request $request, Event $event): View
    {
        $checkedInCount = CheckIn::whereHas('registration', function ($query) use ($event): void {
            $query->where('event_id', $event->id);
        })->count();

        $registeredCount = Registration::where('event_id', $event->id)->count();

        $myRegistration = $request->user()
            ? Registration::with(['waiver', 'checkIn'])
                ->where('user_id', $request->user()->id)
                ->where('event_id', $event->id)
                ->first()
            : null;

        return view('checkin.scan', [
            'event'           => $event,
            'checkedInCount'  => $checkedInCount,
            'registeredCount' => $registeredCount,
            'myRegistration'  => $myRegistration,
        ]);
    }

    /**
     * GET /checkin/{registration} (checkin.show)
     * Individual registration check-in confirmation screen.
     */
    public function show(Request $request, Registration $registration): View
    {
        $user = $request->user();

        if ($registration->user_id !== $user->id && !$user->isHrCoordinator()) {
            abort(403, 'Unauthorized access to this registration check-in record.');
        }

        $registration->load(['event', 'user', 'waiver', 'checkIn']);

        return view('checkin.show', [
            'registration' => $registration,
            'event'        => $registration->event,
            'attendee'     => $registration->user,
        ]);
    }

    /**
     * POST /checkin/{registration}/confirm (checkin.confirm)
     * Confirm check-in for a specific registration and award wellness points once.
     */
    public function confirm(Request $request, Registration $registration): RedirectResponse
    {
        $currentUser = $request->user();

        if ($registration->user_id !== $currentUser->id && !$currentUser->isHrCoordinator()) {
            abort(403, 'Unauthorized check-in attempt.');
        }

        $registration->load(['event', 'user', 'waiver', 'checkIn']);
        $event = $registration->event;
        $attendee = $registration->user;

        if (!$registration->waiver) {
            return back()->withErrors([
                'waiver' => 'Mandatory event waiver has not been signed yet. Please complete the waiver first.',
            ]);
        }

        if ($registration->checkIn) {
            return back()->with('confirmed', [
                'name'               => $attendee->name,
                'event'              => $event->name,
                'points'             => 0,
                'already_checked_in' => true,
            ]);
        }

        $points = (int) ($event->points_checkin ?? 20);

        try {
            DB::transaction(function () use ($registration, $points): void {
                CheckIn::create([
                    'registration_id' => $registration->id,
                    'checked_in_at'   => now(),
                    'method'          => 'qr_scan',
                ]);

                $registration->update(['status' => 'checked_in']);

                $this->awardPointsOnce($registration, 'attended_event', $points);
            });
        } catch (\Throwable $e) {
            return back()->withErrors([
                'checkin' => 'An error occurred while confirming check-in. Please contact the People & Culture desk.',
            ]);
        }

        Cache::forget('hr_overview_data_v21');

        return back()->with('confirmed', [
            'name'               => $attendee->name,
            'event'              => $event->name,
            'points'             => $points,
            'already_checked_in' => false,
        ]);
    }

    /**
     * POST /checkin/manual (checkin.manual)
     * Verify arrival via Staff ID + Event ID (used by Venue Kiosk and Manual Desk Check-In).
     */
    public function manual(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'staff_id' => ['required', 'string', 'max:50'],
        ]);

        $event = Event::findOrFail((int) $data['event_id']);
        $normalizedStaffId = strtolower(trim($data['staff_id']));
        $attendee = User::whereRaw('LOWER(staff_id) = ?', [$normalizedStaffId])->first();

        if (!$attendee) {
            return back()->withInput()->withErrors([
                'staff_id' => 'Staff ID not found in the Strathmore University directory. Please verify and try again.',
            ]);
        }

        $registration = Registration::with(['waiver', 'checkIn'])
            ->where('user_id', $attendee->id)
            ->where('event_id', $event->id)
            ->first();

        if (!$registration) {
            return back()->withInput()->withErrors([
                'staff_id' => 'No active registration found for this Staff ID for ' . $event->name . '.',
            ]);
        }

        if (!$registration->waiver) {
            return back()->withInput()->withErrors([
                'staff_id' => 'Mandatory event waiver not signed. Please sign the event waiver before checking in.',
            ]);
        }

        if ($registration->checkIn) {
            return back()->with('confirmed', [
                'name'               => $attendee->name,
                'event'              => $event->name,
                'points'             => 0,
                'already_checked_in' => true,
            ]);
        }

        $points = (int) ($event->points_checkin ?? 20);

        try {
            DB::transaction(function () use ($registration, $points): void {
                CheckIn::create([
                    'registration_id' => $registration->id,
                    'checked_in_at'   => now(),
                    'method'          => 'manual',
                ]);

                $registration->update(['status' => 'checked_in']);

                $this->awardPointsOnce($registration, 'attended_event', $points);
            });
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'staff_id' => 'System error during manual check-in. Please assist at the People & Culture desk.',
            ]);
        }

        Cache::forget('hr_overview_data_v21');

        return back()->with('confirmed', [
            'name'               => $attendee->name,
            'event'              => $event->name,
            'points'             => $points,
            'already_checked_in' => false,
        ]);
    }

    /**
     * GET /registrations/{registration}/ticket (registrations.ticket)
     * Display the staff member's official event pass / ticket for check-in.
     */
    public function ticket(Request $request, Registration $registration): View
    {
        $user = $request->user();

        if ($registration->user_id !== $user->id && !$user->isHrCoordinator()) {
            abort(403, 'Unauthorized access to this event pass.');
        }

        $registration->load(['event', 'user', 'waiver', 'checkIn']);

        return view('checkin.ticket', [
            'registration' => $registration,
            'event'        => $registration->event,
            'attendee'     => $registration->user,
        ]);
    }

    /**
     * Award wellness points idempotently so duplicate points are never credited.
     */
    private function awardPointsOnce(Registration $registration, string $reason, int $points): void
    {
        $alreadyAwarded = PointsTransaction::where('registration_id', $registration->id)
            ->where('reason', $reason)
            ->exists();

        if ($alreadyAwarded || $points <= 0) {
            return;
        }

        PointsTransaction::create([
            'user_id'         => $registration->user_id,
            'registration_id' => $registration->id,
            'reason'          => $reason,
            'points'          => $points,
        ]);

        $registration->user->increment('points_balance', $points);
    }
}