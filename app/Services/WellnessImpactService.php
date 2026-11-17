<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use App\Models\WellbeingResponse;

class WellnessImpactService
{
    public function impactForEvent(int $eventId): array
    {
        $regIds = Registration::where('event_id', $eventId)->pluck('id');
        if ($regIds->isEmpty()) {
            return ['average_lift' => 0, 'sample_size' => 0, 'improved_pct' => 0];
        }

        $rows = WellbeingResponse::whereIn('registration_id', $regIds)
            ->selectRaw("registration_id,
                MAX(CASE WHEN stage = 'pre'  THEN total_score END) as pre,
                MAX(CASE WHEN stage = 'post' THEN total_score END) as post")
            ->groupBy('registration_id')
            ->havingRaw('pre IS NOT NULL AND post IS NOT NULL')
            ->get();

        if ($rows->isEmpty()) {
            return ['average_lift' => 0, 'sample_size' => 0, 'improved_pct' => 0];
        }

        $lifts    = $rows->map(fn ($r) => (float) $r->post - (float) $r->pre);
        $improved = $lifts->filter(fn ($v) => $v > 0)->count();

        return [
            'average_lift' => round((float) $lifts->avg(), 2),
            'sample_size'  => $rows->count(),
            'improved_pct' => round(($improved / $rows->count()) * 100, 1),
        ];
    }

    public function categoryBreakdown(?string $category = null): array
    {
        $result = [];
        $categories = $category
            ? [$category]
            : ['physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual'];

        foreach ($categories as $cat) {
            $eventIds = Event::where('category', $cat)->pluck('id');
            if ($eventIds->isEmpty()) {
                continue;
            }

            $regIds = Registration::whereIn('event_id', $eventIds)->pluck('id');
            if ($regIds->isEmpty()) {
                continue;
            }

            $rows = WellbeingResponse::whereIn('registration_id', $regIds)
                ->selectRaw("registration_id,
                    MAX(CASE WHEN stage = 'pre'  THEN total_score END) as pre,
                    MAX(CASE WHEN stage = 'post' THEN total_score END) as post")
                ->groupBy('registration_id')
                ->havingRaw('pre IS NOT NULL AND post IS NOT NULL')
                ->get();

            if ($rows->isEmpty()) {
                continue;
            }

            $preAvg  = round((float) $rows->avg('pre'), 1);
            $postAvg = round((float) $rows->avg('post'), 1);
            $lifts   = $rows->map(fn ($r) => (float) $r->post - (float) $r->pre);

            $result[$cat] = [
                'pre_avg'  => $preAvg,
                'post_avg' => $postAvg,
                'avg_lift' => round((float) $lifts->avg(), 1),
                'sample'   => $lifts->count(),
            ];
        }

        return $result;
    }

    public function eventTable(?string $category = null): array
    {
        return Event::query()
            ->when($category, fn ($q, $c) => $q->where('category', $c))
            ->with(['registrations.wellbeingResponses'])
            ->orderByDesc('starts_at')
            ->limit(15)
            ->get()
            ->map(function (Event $event): array {
                $preScores = [];
                $postScores = [];
                $followUpScores = [];

                foreach ($event->registrations as $reg) {
                    $pre    = $reg->wellbeingResponses->firstWhere('stage', 'pre');
                    $post   = $reg->wellbeingResponses->firstWhere('stage', 'post');
                    $follow = $reg->wellbeingResponses->firstWhere('stage', 'follow_up');

                    if ($pre) {
                        $preScores[] = $pre->total_score;
                    }
                    if ($post) {
                        $postScores[] = $post->total_score;
                    }
                    if ($follow) {
                        $followUpScores[] = $follow->total_score;
                    }
                }

                $preAvg  = count($preScores) > 0 ? round(array_sum($preScores) / count($preScores), 1) : null;
                $postAvg = count($postScores) > 0 ? round(array_sum($postScores) / count($postScores), 1) : null;
                $lift    = ($preAvg !== null && $postAvg !== null) ? round($postAvg - $preAvg, 1) : 0;

                return [
                    'id'           => $event->id,
                    'event'        => $event->name,
                    'category'     => $event->category,
                    'pre_average'  => $preAvg,
                    'post_average' => $postAvg,
                    'follow_up'    => count($followUpScores) > 0 ? round(array_sum($followUpScores) / count($followUpScores), 1) : null,
                    'lift'         => $lift,
                    'responses'    => min(count($preScores), count($postScores)),
                ];
            })
            ->toArray();
    }
}