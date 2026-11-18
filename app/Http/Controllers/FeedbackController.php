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
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $registrations = Registration::with(['event', 'feedback', 'wellbeingResponses', 'checkIn'])
            ->where('user_id', auth()->id())
            ->where(function ($query): void {
                $query->whereIn('status', ['checked_in', 'completed'])
                      ->orWhereHas('checkIn');
            })
            ->latest('updated_at')
            ->get()
            ->filter(fn (Registration $r): bool => $r->event !== null)
            ->values();

        $totalAttended  = $registrations->count();
        $totalCompleted = $registrations->filter(fn (Registration $r): bool => $r->status === 'completed' || $r->feedback !== null)->count();
        $pendingCount   = max(0, $totalAttended - $totalCompleted);

        return view('feedback.index', compact('registrations', 'totalAttended', 'totalCompleted', 'pendingCount'));
    }

    public function create(Registration $registration): View|RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $registration->load(['event', 'wellbeingResponses', 'feedback', 'checkIn']);

        if ($registration->checkIn === null && ! in_array($registration->status, ['checked_in', 'completed'], true)) {
            return redirect()
                ->route('checkin.index')
                ->with('error', 'Please complete venue check-in before submitting post-event feedback.');
        }

        if ($registration->status === 'completed' || $registration->feedback !== null) {
            return redirect()
                ->route('feedback.index')
                ->with('success', 'Your Post-Event WHO-5 feedback for "' . ($registration->event->name ?? 'this session') . '" has already been recorded.');
        }

        $preWellbeing = $registration->wellbeingResponses
            ->whereIn('stage', ['pre', 'baseline'])
            ->first();

        return view('feedback.create', compact('registration', 'preWellbeing'));
    }

    public function store(Request $request, Registration $registration): View|RedirectResponse
    {
        abort_unless(auth()->id() === $registration->user_id, 403);

        $registration->load(['event', 'wellbeingResponses', 'feedback', 'checkIn']);

        if ($registration->checkIn === null && ! in_array($registration->status, ['checked_in', 'completed'], true)) {
            return redirect()
                ->route('checkin.index')
                ->with('error', 'Please complete venue check-in before submitting post-event feedback.');
        }

        if ($registration->status === 'completed' || $registration->feedback !== null) {
            return redirect()
                ->route('feedback.index')
                ->with('success', 'Your Post-Event WHO-5 feedback for "' . ($registration->event->name ?? 'this session') . '" has already been recorded.');
        }

        $data = $request->validate(
            [
                'item_cheerful'      => ['required', 'integer', 'between:0,5'],
                'item_calm'          => ['required', 'integer', 'between:0,5'],
                'item_active'        => ['required', 'integer', 'between:0,5'],
                'item_rested'        => ['required', 'integer', 'between:0,5'],
                'item_interested'    => ['required', 'integer', 'between:0,5'],
                'satisfaction_stars' => ['required', 'integer', 'between:1,5'],
                'social_quality'     => ['required', 'integer', 'between:1,10'],
                'comments'           => ['nullable', 'string', 'max:2000'],
            ],
            [
                'item_cheerful.required'      => 'Warning: Please answer Post-Event WHO-5 Question 1 (cheerful and in good spirits).',
                'item_calm.required'          => 'Warning: Please answer Post-Event WHO-5 Question 2 (calm and relaxed).',
                'item_active.required'        => 'Warning: Please answer Post-Event WHO-5 Question 3 (active and vigorous).',
                'item_rested.required'        => 'Warning: Please answer Post-Event WHO-5 Question 4 (woke up feeling fresh and rested).',
                'item_interested.required'    => 'Warning: Please answer Post-Event WHO-5 Question 5 (daily life filled with interesting things).',
                'satisfaction_stars.required' => 'Warning: Please select an Overall Session Satisfaction star rating (1 to 5 stars).',
                'social_quality.required'     => 'Warning: Please rate Social Connection & Session Engagement on the 1 to 10 scale.',
            ]
        );

        $postScore = array_sum([
            (int) $data['item_cheerful'],
            (int) $data['item_calm'],
            (int) $data['item_active'],
            (int) $data['item_rested'],
            (int) $data['item_interested'],
        ]);

        $pointsEarned = (int) ($registration->event->points_feedback ?? 10);

        DB::transaction(function () use ($registration, $data, $postScore, $pointsEarned): void {
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

            $this->awardPointsOnce($registration, 'feedback_submitted', $pointsEarned);
        });

        $preWellbeing   = $registration->wellbeingResponses()->whereIn('stage', ['pre', 'baseline'])->first();
        $preScore       = $preWellbeing ? (int) $preWellbeing->total_score : null;
        $wellbeingDelta = $preScore !== null ? ($postScore - $preScore) : null;

        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));
        $impactPrefix = (string) config('wellloop.cache.hr_impact_prefix', 'hr_impact_data_v6_');
        Cache::forget($impactPrefix);
        if ($registration->event?->category) {
            Cache::forget($impactPrefix . $registration->event->category);
        }

        return view('feedback.success', compact('registration', 'pointsEarned', 'preScore', 'postScore', 'wellbeingDelta'));
    }

    private function awardPointsOnce(Registration $registration, string $reason, int $points): void
    {
        if ($points <= 0) {
            return;
        }

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