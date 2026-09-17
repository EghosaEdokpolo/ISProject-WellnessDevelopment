<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Registration;
use App\Models\WellbeingResponse;
use App\Services\PointsService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function create(Registration $registration): View
    {
        return view('feedback.create', compact('registration'));
    }

    public function store(Request $request, Registration $registration, PointsService $points): RedirectResponse
    {
        // POINTER: 'integer', 'between:0,4' on each WHO-5 item enforces the
        // 0-4 scale (the "At no time" .. "All of the time" buttons) right at
        // validation time — bad data can't even reach the database.
        $data = $request->validate([
            'item_cheerful' => ['required', 'integer', 'between:0,4'],
            'item_calm' => ['required', 'integer', 'between:0,4'],
            'item_active' => ['required', 'integer', 'between:0,4'],
            'item_rested' => ['required', 'integer', 'between:0,4'],
            'item_interested' => ['required', 'integer', 'between:0,4'],
            'satisfaction_stars' => ['required', 'integer', 'between:1,5'],
            'social_quality' => ['required', 'integer', 'between:1,10'],
            'comments' => ['nullable', 'string'],
        ]);

        $items = [
            $data['item_cheerful'], $data['item_calm'], $data['item_active'],
            $data['item_rested'], $data['item_interested'],
        ];

        WellbeingResponse::create([
            'registration_id' => $registration->id,
            'stage' => 'post', // in the full build, 'pre' is captured before the event the same way
            'item_cheerful' => $data['item_cheerful'],
            'item_calm' => $data['item_calm'],
            'item_active' => $data['item_active'],
            'item_rested' => $data['item_rested'],
            'item_interested' => $data['item_interested'],
            'total_score' => WellbeingResponse::score($items),
        ]);

        Feedback::create([
            'registration_id' => $registration->id,
            'satisfaction_stars' => $data['satisfaction_stars'],
            'social_quality' => $data['social_quality'],
            'comments' => $data['comments'] ?? null,
        ]);

        $registration->update(['status' => 'completed']);
        $points->award($registration->user, 'feedback');

        return redirect()->route('rewards.index')->with('status', 'Feedback submitted — points added!');
    }
}
