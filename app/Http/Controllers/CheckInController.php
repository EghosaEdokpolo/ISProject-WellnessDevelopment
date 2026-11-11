<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckInController extends Controller
{
    // 1. Shows the venue arrival page when staff scan the QR code
    public function arrivalForm(Event $event): View
    {
        return view('checkin.arrival', compact('event'));
    }

    // 2. Verifies Staff ID against the DB, checks waiver, marks attended, and shows confirmation
    public function verifyArrival(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'staff_id' => ['required', 'string', 'max:50'],
        ]);

        // Find the staff member in the users table by their Staff ID
        $user = User::where('staff_id', trim($data['staff_id']))->first();

        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['staff_id' => 'Staff ID not found. Please check your ID and try again.']);
        }

        // Check that they registered for this specific event
        $registration = Registration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if (!$registration) {
            return back()
                ->withInput()
                ->withErrors(['staff_id' => 'No registration found for this Staff ID on this event. Please register on WPMIS first.']);
        }

        // Ensure they signed the waiver before marking attendance
        if (!$registration->waiver) {
            return back()
                ->withInput()
                ->withErrors(['staff_id' => 'You have not signed the event waiver yet. Please log in to WPMIS and sign the waiver first.']);
        }

        // Mark attendance if not already checked in
        if (!$registration->checkIn) {
            CheckIn::create([
                'registration_id' => $registration->id,
                'checked_in_at'   => now(),
                'method'          => 'qr',
            ]);

            $registration->update(['status' => 'checked_in']);

            $points = $event->points_checkin ?? 20;
            $this->awardPointsOnce($registration, 'attended_event', $points);
        }

        return back()->with('confirmed', [
            'name'   => $user->name,
            'event'  => $event->name,
            'points' => $event->points_checkin ?? 20,
        ]);
    }

    private function awardPointsOnce(Registration $registration, string $reason, int $points): void
    {
        if (PointsTransaction::where('registration_id', $registration->id)->where('reason', $reason)->exists()) {
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
