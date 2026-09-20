<?php

namespace App\Services;

use App\Models\Event;
use App\Models\WellnessAssessment;

class WellnessImpactService
{
    public function impactForEvent(int $eventId): array
    {
        $rows = WellnessAssessment::where('event_id', $eventId)
            ->selectRaw("staff_id,
                MAX(CASE WHEN assessment_type = 'pre'  THEN who5_score END) as pre,
                MAX(CASE WHEN assessment_type = 'post' THEN who5_score END) as post")
            ->groupBy('staff_id')
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
        $events = Event::query()
            ->when($category, fn ($q, $c) => $q->where('wellness_category', $c))
            ->pluck('id');

        $byCategory = [];
        foreach (['physical', 'mental', 'financial', 'social'] as $cat) {
            $ids = Event::where('wellness_category', $cat)->pluck('id');
            $allRows = collect();
            foreach ($ids as $id) {
                $rows = WellnessAssessment::where('event_id', $id)
                    ->selectRaw("staff_id,
                        MAX(CASE WHEN assessment_type = 'pre'  THEN who5_score END) as pre,
                        MAX(CASE WHEN assessment_type = 'post' THEN who5_score END) as post")
                    ->groupBy('staff_id')
                    ->havingRaw('pre IS NOT NULL AND post IS NOT NULL')
                    ->get();
                $allRows = $allRows->merge($rows);
            }
            $lifts = $allRows->map(fn ($r) => $r->post - $r->pre);
            $byCategory[$cat] = [
                'avg_lift' => $lifts->isEmpty() ? 0 : round($lifts->avg(), 2),
                'sample'   => $lifts->count(),
            ];
        }

        return $byCategory;
    }

    public function eventTable(?string $category = null): array
    {
        return Event::query()
            ->when($category, fn ($q, $c) => $q->where('wellness_category', $c))
            ->orderByDesc('starts_at')
            ->limit(50)
            ->get()
            ->map(function (Event $event) {
                $impact = $this->impactForEvent($event->id);
                return [
                    'id'            => $event->id,
                    'event'         => $event->title,
                    'category'      => $event->wellness_category->value,
                    'pre_average'   => $this->avg($event->id, 'pre'),
                    'post_average'  => $this->avg($event->id, 'post'),
                    'lift'          => $impact['average_lift'],
                    'follow_up'     => $impact['sample_size'] > 0,
                ];
            })
            ->toArray();
    }

    protected function avg(int $eventId, string $type): ?float
    {
        $avg = WellnessAssessment::where('event_id', $eventId)
            ->where('assessment_type', $type)
            ->avg('who5_score');
        return $avg !== null ? round((float) $avg, 2) : null;
    }
}