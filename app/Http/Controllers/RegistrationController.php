<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Waiver;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    // "Register" button on an event card
    public function store(Request $request, Event $event): RedirectResponse
    {
        // POINTER: this checks the ROLE, not just "are you logged in" (that
        // part is already handled by the 'auth' middleware in web.php).
        // Without this, an HR Coordinator account could still hit this URL
        // directly and register as an attendee, muddying the Attendees table.
        abort_unless($request->user()->isStaff(), 403, 'Only staff can register for events.');

        // firstOrCreate: if this user already has a registration for this event,
        // reuse it instead of erroring — makes the button idempotent (safe to
        // click again if e.g. the page reloads).
        $registration = Registration::firstOrCreate([
            'user_id' => $request->user()->id,
            'event_id' => $event->id,
        ]);

        // Next stop in the flow is the indemnity waiver, matching your prototype
        return redirect()->route('registrations.waiver', $registration);
    }

    public function waiverForm(Registration $registration): \Illuminate\View\View
    {
        return view('events.waiver', compact('registration'));
    }

    public function waiverStore(Request $request, Registration $registration, PointsService $points): RedirectResponse
    {
        $data = $request->validate([
            'signature_name' => ['required', 'string', 'max:255'],
            'agree' => ['accepted'], // must be checked — "accepted" requires true/1/"on"/"yes"
        ]);

        Waiver::create([
            'registration_id' => $registration->id,
            'signature_name' => $data['signature_name'],
            'signed_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Consent completion earns points immediately, per your Rewards screen
        $points->award($request->user(), 'consent');

        // Next: the QR code / check-in page
        return redirect()->route('checkin.show', $registration);
    }
}
