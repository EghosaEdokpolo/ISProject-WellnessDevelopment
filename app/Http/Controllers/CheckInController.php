<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CheckInController extends Controller
{
    public function index(): View
    {
        $registrations = Registration::with('event')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['registered', 'checked_in'])
            ->whereHas('event', fn ($q) => $q->where('starts_at', '>=', now()->startOfDay()))
            ->latest()
            ->get();

        return view('checkin.index', compact('registrations'));
    }

    public function ticket(Registration $registration): View
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        
        $qrUrl = URL::temporarySignedRoute('checkin.confirm', now()->addMinutes(15), ['registration' => $registration->id]);
        $qrSvg = QrCode::format('svg')->size(250)->generate($qrUrl);
        
        return view('registrations.ticket', compact('registration', 'qrSvg', 'qrUrl'));
    }

    public function scan(Event $event): RedirectResponse
    {
        $registration = Registration::where('user_id', auth()->id())->where('event_id', $event->id)->first();
        if (!$registration) {
            return redirect()->route('events.index')->with('error', 'You are not registered for this event.');
        }

        return redirect()->route('checkin.show', $registration);
    }

    public function show(Registration $registration): View
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        return view('checkin.show', compact('registration'));
    }

    public function confirm(Registration $registration): RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $event = $registration->event;

        if (!now()->between($event->starts_at->copy()->startOfDay(), $event->starts_at->copy()->addDay()->endOfDay())) {
            return back()->withErrors(['error' => 'Check-in is only available on the day of the event.']);
        }

        if (!$registration->checkIn) {
            CheckIn::create(['registration_id' => $registration->id, 'checked_in_at' => now(), 'method' => 'qr']);
            $registration->update(['status' => 'checked_in']);
            $this->awardPointsOnce($registration, 'attended_event', 20);
        }

        return redirect()->route('feedback.create', $registration)->with('status', 'Checked in successfully! Please complete your feedback.');
    }

    public function manual(Request $request): RedirectResponse
    {
        // CHANGED: Now looking for staff_id instead of employee_id
        $data = $request->validate(['staff_id' => ['required', 'string']]);
        
        $user = User::where('staff_id', $data['staff_id'])->first();
        
        if (!$user) {
            return back()->withErrors(['staff_id' => 'Staff ID not found.']);
        }

        $registration = Registration::where('user_id', $user->id)->whereIn('status', ['registered', 'checked_in'])->latest()->first();
        
        if (!$registration) {
            return back()->withErrors(['staff_id' => 'No active registration.']);
        }

        CheckIn::updateOrCreate(['registration_id' => $registration->id], ['checked_in_at' => now(), 'method' => 'manual']);
        $registration->update(['status' => 'checked_in']);
        $this->awardPointsOnce($registration, 'attended_event', 20);

        return back()->with('status', 'Check-in successful for ' . $user->name);
    }

    private function awardPointsOnce(Registration $registration, string $reason, int $points): void
    {
        if (PointsTransaction::where('registration_id', $registration->id)->where('reason', $reason)->exists()) return;
        PointsTransaction::create(['user_id' => $registration->user_id, 'registration_id' => $registration->id, 'reason' => $reason, 'points' => $points]);
        $registration->user->increment('points_balance', $points);
    }
}