<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Models\Waiver;
use App\Services\CheckInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use RuntimeException;

class CheckInController extends Controller
{
    public function __construct(
        private readonly CheckInService $checkInService
    ) {}

    public function index(Request $request): View
    {
        $user             = $request->user();
        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 10);
        $cutoff           = now($tz)->subMinutes($lateGraceMinutes);

        $registrations = Registration::with(['event', 'waiver', 'checkIn', 'feedback'])
            ->where('user_id', $user->id)
            ->get()
            ->filter(fn (Registration $registration): bool => $registration->event !== null)
            ->values();

        // Auto-sync digital waiver for any registration where consent_given was already set (e.g. seeded records)
        foreach ($registrations as $reg) {
            if (! $reg->waiver && $reg->consent_given) {
                $waiver = Waiver::firstOrCreate(
                    ['registration_id' => $reg->id],
                    [
                        'signature_name' => $user->name,
                        'signed_at'      => $reg->created_at ?? now(),
                    ]
                );
                $reg->setRelation('waiver', $waiver);
            }
        }

        // Only show upcoming or currently open events under "Your Registered Events" (Fixes Photo 1)
        $activeRegistrations = $registrations
            ->filter(function (Registration $registration) use ($tz, $cutoff): bool {
                if ($registration->checkIn !== null || in_array($registration->status, ['checked_in', 'completed'], true)) {
                    return false;
                }

                $startsAt = $registration->event->starts_at?->copy()->shiftTimezone($tz);
                return $startsAt !== null && $startsAt->gt($cutoff);
            })
            ->sortBy(fn (Registration $registration) => $registration->event->starts_at->timestamp)
            ->values();

        $pastCheckIns = $registrations
            ->filter(fn (Registration $registration): bool => $registration->checkIn !== null || in_array($registration->status, ['checked_in', 'completed'], true))
            ->sortByDesc(fn (Registration $registration) => $registration->checkIn?->checked_in_at?->timestamp ?? $registration->event->starts_at?->timestamp ?? 0)
            ->values();

        $upcomingEvents = Event::where('starts_at', '>', $cutoff)
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

    public function poster(Event $event): View
    {
        $scanUrl = URL::signedRoute('checkin.scan', ['event' => $event->id]);

        return view('events.qr_poster', compact('event', 'scanUrl'));
    }

    public function scan(Request $request, Event $event): View
    {
        $user = $request->user();
        $tz   = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $gate = $this->checkInService->evaluateGateStatus($event);

        if ($user->isHrCoordinator() && ! $request->hasValidSignature()) {
            $registrations = Registration::with(['user', 'checkIn', 'waiver', 'feedback'])
                ->where('event_id', $event->id)
                ->get()
                ->sortByDesc(fn (Registration $reg) => $reg->checkIn?->checked_in_at?->timestamp ?? 0)
                ->values();

            $checkedInCount  = $registrations->filter(fn (Registration $r) => $r->checkIn !== null || in_array($r->status, ['checked_in', 'completed'], true))->count();
            $registeredCount = $registrations->count();

            return view('checkin.scan', [
                'event'           => $event,
                'tz'              => $tz,
                'gate'            => $gate,
                'registrations'   => $registrations,
                'checkedInCount'  => $checkedInCount,
                'registeredCount' => $registeredCount,
                'myRegistration'  => null,
            ]);
        }

        abort_unless($request->hasValidSignature(), 403, 'Please scan the official QR poster at the event venue to check in.');

        try {
            $outcome = $this->checkInService->processVenueQrScan($user, $event);

            return view('events.qr_scan_result', [
                'event'            => $event,
                'registration'     => $outcome['registration'],
                'autoRegistered'   => $outcome['auto_registered'],
                'alreadyCheckedIn' => $outcome['already_checked_in'],
                'pointsEarned'     => $outcome['points_earned'],
                'isLate'           => $outcome['is_late'],
                'minutesLate'      => $outcome['minutes_late'],
                'basePoints'       => $outcome['base_points'],
                'pointsReducedBy'  => $outcome['points_reduced_by'],
                'needsOnboarding'  => $outcome['needs_onboarding'],
                'scanError'        => null,
            ]);
        } catch (RuntimeException $e) {
            return view('events.qr_scan_result', [
                'event'            => $event,
                'registration'     => null,
                'autoRegistered'   => false,
                'alreadyCheckedIn' => false,
                'pointsEarned'     => 0,
                'isLate'           => false,
                'minutesLate'      => 0,
                'basePoints'       => (int) ($event->points_checkin ?? 25),
                'pointsReducedBy'  => 0,
                'needsOnboarding'  => false,
                'scanError'        => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Unexpected failure during venue QR scan', [
                'user_id'  => $user->id,
                'event_id' => $event->id,
                'error'    => $e->getMessage(),
            ]);

            return view('events.qr_scan_result', [
                'event'            => $event,
                'registration'     => null,
                'autoRegistered'   => false,
                'alreadyCheckedIn' => false,
                'pointsEarned'     => 0,
                'isLate'           => false,
                'minutesLate'      => 0,
                'basePoints'       => (int) ($event->points_checkin ?? 25),
                'pointsReducedBy'  => 0,
                'needsOnboarding'  => false,
                'scanError'        => 'We could not complete your check-in right now. Please notify the People & Culture desk at the venue.',
            ]);
        }
    }

    public function show(Request $request, Registration $registration): View
    {
        $user = $request->user();

        if ($registration->user_id !== $user->id && ! $user->isHrCoordinator()) {
            abort(403, 'Unauthorized access to this registration check-in record.');
        }

        $registration->load(['event', 'user', 'waiver', 'checkIn']);

        return view('checkin.show', [
            'registration' => $registration,
            'event'        => $registration->event,
            'attendee'     => $registration->user,
        ]);
    }

    public function confirm(Request $request, Registration $registration): RedirectResponse
    {
        $currentUser = $request->user();

        if ($registration->user_id !== $currentUser->id && ! $currentUser->isHrCoordinator()) {
            abort(403, 'Unauthorized check-in attempt.');
        }

        try {
            $confirmed = $this->checkInService->confirmRegistrationCheckIn($registration, 'qr_scan', $currentUser);

            return back()->with('confirmed', $confirmed);
        } catch (RuntimeException $e) {
            return back()->withErrors(['waiver' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Unexpected failure confirming registration check-in', [
                'registration_id' => $registration->id,
                'actor_id'        => $currentUser->id,
                'error'           => $e->getMessage(),
            ]);

            return back()->withErrors([
                'checkin' => 'An error occurred while confirming check-in. Please contact the People & Culture desk.',
            ]);
        }
    }

    public function manual(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isHrCoordinator(), 403, 'Only HR Coordinators can perform manual Staff ID check-ins.');

        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'staff_id' => ['required', 'string', 'max:50'],
        ]);

        $event             = Event::findOrFail((int) $data['event_id']);
        $normalizedStaffId = strtolower(trim($data['staff_id']));
        $attendee          = User::whereRaw('LOWER(staff_id) = ?', [$normalizedStaffId])->first();

        if (! $attendee) {
            return back()->withInput()->withErrors([
                'staff_id' => 'Staff ID not found in the Strathmore University directory. Please verify and try again.',
            ]);
        }

        $registration = Registration::where('user_id', $attendee->id)
            ->where('event_id', $event->id)
            ->first();

        if (! $registration) {
            return back()->withInput()->withErrors([
                'staff_id' => 'No active registration found for this Staff ID for ' . $event->name . '.',
            ]);
        }

        try {
            $confirmed = $this->checkInService->confirmRegistrationCheckIn($registration, 'manual', $request->user());

            return back()->with('confirmed', $confirmed);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['staff_id' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Unexpected failure during manual HR check-in', [
                'staff_id' => $normalizedStaffId,
                'event_id' => $event->id,
                'actor_id' => $request->user()->id,
                'error'    => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'staff_id' => 'System error during manual check-in. Please assist at the People & Culture desk.',
            ]);
        }
    }

    public function ticket(Request $request, Registration $registration): View
    {
        $user = $request->user();

        if ($registration->user_id !== $user->id && ! $user->isHrCoordinator()) {
            abort(403, 'Unauthorized access to this event pass.');
        }

        $registration->load(['event', 'user', 'waiver', 'checkIn', 'feedback']);
        $gate = $this->checkInService->evaluateGateStatus($registration->event);

        return view('checkin.ticket', [
            'registration' => $registration,
            'event'        => $registration->event,
            'attendee'     => $registration->user,
            'gate'         => $gate,
        ]);
    }
}