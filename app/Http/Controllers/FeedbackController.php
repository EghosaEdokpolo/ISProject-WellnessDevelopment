<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\WellbeingResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $registrations = Registration::with(['event', 'feedback', 'wellbeingResponses'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['checked_in', 'completed'])
            ->latest('updated_at')
            ->get();

        $totalAttended = $registrations->count();
        $totalCompleted = $registrations->where('status', 'completed')->count();
        $pendingCount = max(0, $totalAttended - $totalCompleted);

        return view('feedback.index', compact('registrations', 'totalAttended', 'totalCompleted', 'pendingCount'));
    }

    public function create(Registration $registration): View|RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        if (! in_array($registration->status, ['checked_in', 'completed'], true)) {
            return redirect()
                ->route('checkin.index')
                ->with('error', 'Please complete venue check-in before submitting post-event feedback.');
        }

        $registration->load(['event', 'wellbeingResponses']);
        $preWellbeing = $registration->wellbeingResponses
            ->whereIn('stage', ['pre', 'baseline'])
            ->first();

        return view('feedback.create', compact('registration', 'preWellbeing'));
    }

    public function store(Request $request, Registration $registration): View|RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        if (! in_array($registration->status, ['checked_in', 'completed'], true)) {
            return redirect()
                ->route('checkin.index')
                ->with('error', 'Please complete venue check-in before submitting post-event feedback.');
        }

        $data = $request->validate([
            'item_cheerful'      => 'required|integer|between:0,5',
            'item_calm'          => 'required|integer|between:0,5',
            'item_active'        => 'required|integer|between:0,5',
            'item_rested'        => 'required|integer|between:0,5',
            'item_interested'    => 'required|integer|between:0,5',
            'satisfaction_stars' => 'required|integer|between:1,5',
            'social_quality'     => 'required|integer|between:1,10',
            'comments'           => 'nullable|string|max:2000',
        ]);

        $postScore = array_sum([
            (int) $data['item_cheerful'],
            (int) $data['item_calm'],
            (int) $data['item_active'],
            (int) $data['item_rested'],
            (int) $data['item_interested'],
        ]);

        WellbeingResponse::updateOrCreate(
            ['registration_id' => $registration->id, 'stage' => 'post'],
            [
                'item_cheerful'   => (int) $data['item_cheerful'],
                'item_calm'       => (int) $data['item_calm'],
                'item_active'     => (int) $data['item_active'],
                'item_rested'     => (int) $data['item_rested'],
                'item_interested' => (int) $data['item_interested'],
                'total_score'     => $postScore,
            ]
        );

        Feedback::updateOrCreate(
            ['registration_id' => $registration->id],
            [
                'satisfaction_stars' => (int) $data['satisfaction_stars'],
                'social_quality'     => (int) $data['social_quality'],
                'comments'           => $data['comments'] ?? null,
            ]
        );

        $registration->update(['status' => 'completed']);

        $pointsEarned = (int) ($registration->event->points_feedback ?? 10);
        $this->awardPointsOnce($registration, 'feedback_submitted', $pointsEarned);

        $preWellbeing = $registration->wellbeingResponses()
            ->whereIn('stage', ['pre', 'baseline'])
            ->first();
        $preScore = $preWellbeing ? (int) $preWellbeing->total_score : null;
        $wellbeingDelta = $preScore !== null ? ($postScore - $preScore) : null;

        Cache::forget('hr_overview_data_v21');
        Cache::forget('hr_impact_data_v6_');
        if ($registration->event?->category) {
            Cache::forget('hr_impact_data_v6_' . $registration->event->category);
        }

        return view('feedback.success', compact('registration', 'pointsEarned', 'preScore', 'postScore', 'wellbeingDelta'));
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