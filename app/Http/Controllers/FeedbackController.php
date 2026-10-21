<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\WellbeingResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    // Shows the list of events the user attended
    public function index(): View
    {
        $registrations = Registration::with(['event', 'feedback'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['checked_in', 'completed'])
            ->latest()
            ->get();
            
        return view('feedback.index', compact('registrations'));
    }

    public function create(Registration $registration): View
    {
        abort_unless(auth()->id() === $registration->user_id, 403);
        return view('feedback.create', compact('registration'));
    }

    public function store(Request $request, Registration $registration): View|RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $data = $request->validate([
            'item_cheerful' => ['required', 'integer', 'between:0,4'],
            'item_calm' => ['required', 'integer', 'between:0,4'],
            'item_active' => ['required', 'integer', 'between:0,4'],
            'item_rested' => ['required', 'integer', 'between:0,4'],
            'item_interested' => ['required', 'integer', 'between:0,4'],
            'satisfaction_stars' => ['required', 'integer', 'between:1,5'],
            'social_quality' => ['required', 'integer', 'between:1,10'],
            'comments' => ['nullable', 'string', 'max:2000'],
        ]);

        $total = array_sum([
            $data['item_cheerful'], 
            $data['item_calm'], 
            $data['item_active'], 
            $data['item_rested'], 
            $data['item_interested']
        ]);

        WellbeingResponse::updateOrCreate(
            ['registration_id' => $registration->id, 'stage' => 'post'], 
            [
                'item_cheerful' => $data['item_cheerful'], 
                'item_calm' => $data['item_calm'], 
                'item_active' => $data['item_active'],
                'item_rested' => $data['item_rested'], 
                'item_interested' => $data['item_interested'], 
                'total_score' => $total
            ]
        );

        Feedback::updateOrCreate(
            ['registration_id' => $registration->id], 
            [
                'satisfaction_stars' => $data['satisfaction_stars'], 
                'social_quality' => $data['social_quality'], 
                'comments' => $data['comments'] ?? null
            ]
        );

        $registration->update(['status' => 'completed']);
        
        $this->awardPointsOnce($registration, 'feedback_submitted', 5);

        // Loads the Success Page you provided!
        return view('feedback.success'); 
    }

    private function awardPointsOnce(Registration $registration, string $reason, int $points): void
    {
        if (PointsTransaction::where('registration_id', $registration->id)->where('reason', $reason)->exists()) return;
        PointsTransaction::create(['user_id' => $registration->user_id, 'registration_id' => $registration->id, 'reason' => $reason, 'points' => $points]);
        $registration->user->increment('points_balance', $points);
    }
}