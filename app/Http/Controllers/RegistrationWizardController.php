<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\Waiver;
use App\Models\WellbeingResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class RegistrationWizardController extends Controller
{
    public function show(Event $event)
    {
        // Check if already registered
        $existing = Registration::where('user_id', auth()->id())->where('event_id', $event->id)->first();
        if ($existing) {
            return redirect()->route('checkin.show', $existing)->with('status', 'You are already registered for this event.');
        }

        return view('registration.wizard', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'dietary_requirements' => ['nullable', 'string', 'max:255'],
            // WHO-5 Pre Scores
            'item_cheerful' => ['required', 'integer', 'between:0,4'],
            'item_calm' => ['required', 'integer', 'between:0,4'],
            'item_active' => ['required', 'integer', 'between:0,4'],
            'item_rested' => ['required', 'integer', 'between:0,4'],
            'item_interested' => ['required', 'integer', 'between:0,4'],
            // Signatures
            'signature_name' => ['required', 'string', 'max:255'],
            'agree_waiver' => ['accepted'],
            'agree_consent' => ['accepted'],
        ]);

        $user = auth()->user();
        // Update user details just in case
        $user->update(['name' => $data['name'], 'department' => $data['department']]);

        // 1. Create Registration
        $registration = Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'registered',
            'dietary_requirements' => $data['dietary_requirements'],
        ]);

        // 2. Save Pre-WHO5
        $total = array_sum([
            $data['item_cheerful'], $data['item_calm'], $data['item_active'], 
            $data['item_rested'], $data['item_interested']
        ]);

        WellbeingResponse::create([
            'registration_id' => $registration->id,
            'stage' => 'pre',
            'item_cheerful' => $data['item_cheerful'],
            'item_calm' => $data['item_calm'],
            'item_active' => $data['item_active'],
            'item_rested' => $data['item_rested'],
            'item_interested' => $data['item_interested'],
            'total_score' => $total,
        ]);

        // 3. Save Waiver
        Waiver::create([
            'registration_id' => $registration->id,
            'signature_name' => $data['signature_name'],
            'signed_at' => now(),
        ]);

        // 4. Award Points for Registration/Waiver
        PointsTransaction::create([
            'user_id' => $user->id,
            'registration_id' => $registration->id,
            'reason' => 'consent',
            'points' => 5,
        ]);
        $user->increment('points_balance', 5);

        // Redirect to the Check-in page which will now show their Ticket QR
        return redirect()->route('checkin.show', $registration)->with('status', 'Registration complete! Here is your Ticket QR.');
    }
}