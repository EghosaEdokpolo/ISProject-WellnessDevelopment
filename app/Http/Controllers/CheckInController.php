<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class CheckInController extends Controller
{
    public function show(Registration $registration): View
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        $qrUrl = URL::temporarySignedRoute('checkin.confirm', now()->addMinutes(15), ['registration' => $registration->id]);
        return view('checkin.show', compact('registration', 'qrUrl'));
    }

    public function confirm(Registration $registration): RedirectResponse
    {
        if (!$registration->checkIn) {
            CheckIn::create(['registration_id' => $registration->id, 'checked_in_at' => now(), 'method' => 'qr']);
            $registration->update(['status' => 'checked_in']);
            $this->awardPointsOnce($registration, 'attended_event', 10);
        }
        return redirect()->route('feedback.create', $registration)->with('status', 'Checked in!');
    }

    public function manual(Request $request): RedirectResponse
    {
        $data = $request->validate(['employee_id' => ['required', 'string']]);
        $user = User::where('employee_id', $data['employee_id'])->first();
        if (!$user) return back()->withErrors(['employee_id' => 'Staff not found.']);

        $registration = Registration::where('user_id', $user->id)->whereIn('status', ['registered', 'checked_in'])->latest()->first();
        if (!$registration) return back()->withErrors(['employee_id' => 'No active registration.']);

        CheckIn::updateOrCreate(['registration_id' => $registration->id], ['checked_in_at' => now(), 'method' => 'manual']);
        $registration->update(['status' => 'checked_in']);
        $this->awardPointsOnce($registration, 'attended_event', 10);

        return back()->with('status', 'Check-in successful for ' . $user->name);
    }

    private function awardPointsOnce(Registration $registration, string $reason, int $points): void
    {
        if (PointsTransaction::where('registration_id', $registration->id)->where('reason', $reason)->exists()) return;
        PointsTransaction::create(['user_id' => $registration->user_id, 'registration_id' => $registration->id, 'reason' => $reason, 'points' => $points]);
        $registration->user->increment('points_balance', $points);
    }
}