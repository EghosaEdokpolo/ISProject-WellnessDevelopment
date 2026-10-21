<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckInController extends Controller
{
    // 1. Shows the list of events the staff can check into
    public function index(): View
    {
        $registrations = Registration::with('event')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['registered', 'checked_in'])
            ->latest()
            ->get();
            
        return view('checkin.index', compact('registrations'));
    }

    // 2. When they scan the printed HR QR Code
    public function scan(Event $event): RedirectResponse
    {
        $registration = Registration::where('user_id', auth()->id())
            ->where('event_id', $event->id)
            ->first();

        if (!$registration) {
            return redirect()->route('events.index')
                ->with('error', 'You are not registered for this event. Please register first.');
        }

        return redirect()->route('checkin.show', $registration);
    }

    // 3. The simulated scan screen
    public function show(Registration $registration): View
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        return view('checkin.show', compact('registration'));
    }

    // 4. The confirm button logic
    public function confirm(Registration $registration): RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        if (!$registration->waiver) {
            return redirect()->route('registrations.waiver', $registration)
                ->with('error', 'You must sign the waiver before checking in.');
        }

        if (!$registration->checkIn) {
            CheckIn::create([
                'registration_id' => $registration->id,
                'checked_in_at' => now(),
                'method' => 'qr'
            ]);
            
            $registration->update(['status' => 'checked_in']);
            $this->awardPointsOnce($registration, 'attended_event', 20);
        }

        return redirect()->route('feedback.create', $registration)
            ->with('status', 'Checked in successfully! Please complete your feedback.');
    }

    private function awardPointsOnce(Registration $registration, string $reason, int $points): void
    {
        if (PointsTransaction::where('registration_id', $registration->id)->where('reason', $reason)->exists()) return;
        
        PointsTransaction::create([
            'user_id' => $registration->user_id, 
            'registration_id' => $registration->id, 
            'reason' => $reason, 
            'points' => $points
        ]);
        
        $registration->user->increment('points_balance', $points);
    }
}