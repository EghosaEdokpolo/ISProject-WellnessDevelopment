<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class CheckInController extends Controller
{
    // Shows the "You are registered! Present this QR code..." screen
    public function show(Registration $registration): View
    {
        // POINTER: URL::temporarySignedRoute builds a URL with a cryptographic
        // signature baked in, PLUS an expiry time. Laravel checks both
        // automatically when the URL is visited (see the 'signed' middleware
        // on the route in web.php). This gives you the "refreshes every 15
        // minutes, screenshot won't work after expiry" behaviour for free —
        // no need to hand-roll token generation or a cron job to expire codes.
        $qrUrl = URL::temporarySignedRoute(
            'checkin.confirm',
            now()->addMinutes(15),
            ['registration' => $registration->id]
        );

        return view('checkin.show', compact('registration', 'qrUrl'));
    }

    // The URL encoded IN the QR code hits this — must carry a valid signature
    public function confirm(Request $request, Registration $registration): RedirectResponse
    {
        return $this->recordCheckIn($registration, 'qr');
    }

    // "No smartphone? Enter EMP-104 manually" — the accessibility fallback.
    // A staff member at the check-in desk types the employee_id in, no QR needed.
    public function manual(Request $request): RedirectResponse
    {
        $data = $request->validate(['employee_id' => ['required', 'string']]);

        $user = \App\Models\User::where('employee_id', $data['employee_id'])->firstOrFail();

        $registration = Registration::where('user_id', $user->id)
            ->whereHas('event', fn ($q) => $q->where('starts_at', '>=', now()->subHours(2)))
            ->latest()
            ->firstOrFail();

        return $this->recordCheckIn($registration, 'manual');
    }

    private function recordCheckIn(Registration $registration, string $method): RedirectResponse
    {
        // updateOrCreate: re-scanning an already-checked-in QR just no-ops
        // instead of creating duplicate check-in rows.
        CheckIn::updateOrCreate(
            ['registration_id' => $registration->id],
            ['checked_in_at' => now(), 'method' => $method]
        );

        $registration->update(['status' => 'checked_in']);

        app(\App\Services\PointsService::class)->award($registration->user, 'check_in');

        return redirect()->route('feedback.create', $registration)
            ->with('status', 'Checked in! Points added.');
    }
}
