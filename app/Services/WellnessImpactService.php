<?php

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

        $lifts    = $rows->map(fn ($r) => $r->post - $r->pre);
        $improved = $lifts->filter(fn ($v) => $v > 0)->count();

        return [
            'average_lift' => round($lifts->avg(), 2),
            'sample_size'  => $rows->count(),
            'improved_pct' => round(($improved / $rows->count()) * 100, 1),
        ];
    }

    public function categoryBreakdown(?string $category = null): array
    {
        $result = [];

        foreach (['physical', 'mental', 'financial', 'social'] as $cat) {
            $eventIds = Event::where('category', $cat)->pluck('id');
            $regIds   = Registration::whereIn('event_id', $eventIds)->pluck('id');

            if ($regIds->isEmpty()) {
                $result[$cat] = ['avg_lift' => 0, 'sample' => 0];
                continue;
            }

            $rows = WellbeingResponse::whereIn('registration_id', $regIds)
                ->selectRaw("registration_id,
                    MAX(CASE WHEN stage = 'pre'  THEN total_score END) as pre,
                    MAX(CASE WHEN stage = 'post' THEN total_score END) as post")
                ->groupBy('registration_id')
                ->havingRaw('pre IS NOT NULL AND post IS NOT NULL')
                ->get();

            $lifts = $rows->map(fn ($r) => $r->post - $r->pre);

            $result[$cat] = [
                'avg_lift' => $lifts->isEmpty() ? 0 : round($lifts->avg(), 2),
                'sample'   => $lifts->count(),
            ];
        }

        return $result;
    }

    public function eventTable(?string $category = null): array
    {
        return Event::query()
            ->when($category, fn ($q, $c) => $q->where('category', $c))
            ->orderByDesc('starts_at')
            ->limit(50)
            ->get()
            ->map(function (Event $event) {
                $impact = $this->impactForEvent($event->id);
                $regIds = Registration::where('event_id', $event->id)->pluck('id');

                $preAvg  = $regIds->isEmpty() ? null : WellbeingResponse::whereIn('registration_id', $regIds)
                    ->where('stage', 'pre')->avg('total_score');
                $postAvg = $regIds->isEmpty() ? null : WellbeingResponse::whereIn('registration_id', $regIds)
                    ->where('stage', 'post')->avg('total_score');

                return [
                    'id'           => $event->id,
                    'event'        => $event->name,
                    'category'     => $event->category,
                    'pre_average'  => $preAvg !== null ? round((float) $preAvg, 2) : null,
                    'post_average' => $postAvg !== null ? round((float) $postAvg, 2) : null,
                    'lift'         => $impact['average_lift'],
                    'follow_up'    => $impact['sample_size'] > 0,
                ];
            })
            ->toArray();
    }
}