<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\Waiver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $user = $request->user();
        $existing = Registration::where('user_id', $user->id)->where('event_id', $event->id)->first();
        if ($existing) return redirect()->route('registrations.waiver', $existing)->with('status', 'Already registered.');

        $registration = Registration::create(['user_id' => $user->id, 'event_id' => $event->id, 'status' => 'registered']);
        return redirect()->route('registrations.waiver', $registration)->with('status', 'Registered! Please sign the waiver.');
    }

    public function waiverForm(Registration $registration): View
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        return view('events.waiver', compact('registration'));
    }

    public function waiverStore(Request $request, Registration $registration): RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        $data = $request->validate(['signature_name' => ['required', 'string', 'max:255'], 'agree' => ['accepted']]);

        Waiver::updateOrCreate(['registration_id' => $registration->id], ['signature_name' => $data['signature_name'], 'signed_at' => now()]);
        $this->awardPointsOnce($registration, 'waiver_signed', 5);

        return redirect()->route('checkin.show', $registration)->with('status', 'Waiver signed.');
    }

    private function awardPointsOnce(Registration $registration, string $reason, int $points): void
    {
        if (PointsTransaction::where('registration_id', $registration->id)->where('reason', $reason)->exists()) return;
        PointsTransaction::create(['user_id' => $registration->user_id, 'registration_id' => $registration->id, 'reason' => $reason, 'points' => $points]);
        $registration->user->increment('points_balance', $points);
    }
}