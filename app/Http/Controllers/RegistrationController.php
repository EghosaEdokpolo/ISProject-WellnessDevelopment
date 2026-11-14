<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\Waiver;
use App\Models\WellbeingResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(Event $event): View
    {
        if ($event->starts_at->isPast()) {
            abort(403, 'Registration for this event has closed.');
        }

        return view('registrations.details', compact('event'));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        if ($event->starts_at->isPast()) {
            return redirect()->route('events.index')->with('error', 'Registration for this event has closed.');
        }

        $user = $request->user();
        
        $existing = Registration::where('user_id', $user->id)->where('event_id', $event->id)->first();
        if ($existing) {
            return redirect()->route('registrations.who5', $existing)->with('status', 'Continuing your registration...');
        }

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'dietary_requirements' => 'nullable|string|max:255',
        ]);

        $user->update(['name' => $data['full_name'], 'department' => $data['department']]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'registered',
            'dietary_requirements' => $data['dietary_requirements'],
        ]);

        return redirect()->route('registrations.who5', $registration);
    }

    public function who5Form(Registration $registration): View
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        return view('registrations.who5', compact('registration'));
    }

    public function who5Store(Request $request, Registration $registration): RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $data = $request->validate([
            'item_cheerful' => 'required|integer|between:0,4',
            'item_calm' => 'required|integer|between:0,4',
            'item_active' => 'required|integer|between:0,4',
            'item_rested' => 'required|integer|between:0,4',
            'item_interested' => 'required|integer|between:0,4',
        ]);

        $total = array_sum($data);

        // Removed 'assessed_at' here
        WellbeingResponse::updateOrCreate(
            ['registration_id' => $registration->id, 'stage' => 'pre'],
            array_merge($data, ['total_score' => $total])
        );

        return redirect()->route('registrations.waiver', $registration);
    }

    public function waiverForm(Registration $registration): View
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        return view('registrations.waiver', compact('registration'));
    }

    public function waiverStore(Request $request, Registration $registration): RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $data = $request->validate([
            'signature_name' => 'required|string|max:255',
            'agree' => 'accepted',
        ]);

        Waiver::updateOrCreate(
            ['registration_id' => $registration->id],
            ['signature_name' => $data['signature_name'], 'signed_at' => now()]
        );

        return redirect()->route('registrations.consent', $registration);
    }

    public function consentForm(Registration $registration): View
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        return view('registrations.consent', compact('registration'));
    }

    public function consentStore(Request $request, Registration $registration): RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $request->validate(['agree' => 'accepted']);

        $registration->update([
            'consent_given' => true,
            'consent_given_at' => now(),
        ]);

        if (!PointsTransaction::where('registration_id', $registration->id)->where('reason', 'waiver_signed')->exists()) {
            PointsTransaction::create([
                'user_id' => $registration->user_id,
                'registration_id' => $registration->id,
                'reason' => 'waiver_signed',
                'points' => 5,
            ]);
            $registration->user->increment('points_balance', 5);
        }

        return redirect()->route('registrations.ticket', $registration);
    }
}