<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use App\Models\WellbeingResponse;

class WellnessImpactService
{
    /**
     * Get all 8 SAMHSA Wellness Dimensions.
     */
    public function getDimensions(): array
    {
        return [
            'physical', 'mental', 'financial', 'social', 
            'intellectual', 'occupational', 'environmental', 'spiritual'
        ];
    }

    /* Compute impact analytics data for a specific single event.*/
    public function impactForEvent(int $eventId): array
    {
        $regIds = Registration::where('event_id', $eventId)->pluck('id');
        if ($regIds->isEmpty()) {
            return ['average_lift' => 0, 'sample_size' => 0, 'attendees_count' => 0];
        }

        $rows = WellbeingResponse::whereIn('registration_id', $regIds)
            ->selectRaw("registration_id, MAX(CASE WHEN stage = 'pre' THEN total_score END) as pre, MAX(CASE WHEN stage = 'post' THEN total_score END) as post")
            ->groupBy('registration_id')
            ->havingRaw('pre IS NOT NULL AND post IS NOT NULL')
            ->get();

        if ($rows->isEmpty()) {
            return ['average_lift' => 0, 'sample_size' => 0, 'attendees_count' => $regIds->count()];
        }

        $lifts = $rows->map(fn ($r) => $r->post - $r->pre);

        return [
            'average_lift' => round($lifts->avg(), 1),
            'sample_size' => $rows->count(),
            'attendees_count' => $regIds->count()
        ];
    }

    /**
     * Calculate Lift breakdowns across all 12 calendar months for the chart.
     */
    public function categoryBreakdown(?string $category = null): array
    {
        $result = [];
        
        // Define standard chronological monthly buckets for our timeline axis
        $months = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        foreach ($months as $index => $monthName) {
            $monthNumber = $index + 1;

            // Build a base query that looks across evaluations conducted in this specific month
            $queryBase = WellbeingResponse::whereMonth('created_at', $monthNumber);

            // If an individual category pill is selected, filter responses down to that type
            if ($category) {
                $queryBase->whereHas('registration.event', function ($q) use ($category) {
                    $q->where('category', $category);
                });
            }

            // Extract calculations
            $preAvg  = (clone $queryBase)->where('stage', 'pre')->avg('total_score') ?? 0;
            $postAvg = (clone $queryBase)->where('stage', 'post')->avg('total_score') ?? 0;

            $result[$monthName] = [
                'avg_lift' => round($postAvg - $preAvg, 1),
                'pre_avg'  => round($preAvg, 1),
                'post_avg' => round($postAvg, 1)
            ];
        }

        return $result;
    }


    /**
     * Generate table rows matching your UI table parameters.
     */
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
                
                $preAvg = $regIds->isEmpty() ? null : WellbeingResponse::whereIn('registration_id', $regIds)->where('stage', 'pre')->avg('total_score');
                $postAvg = $regIds->isEmpty() ? null : WellbeingResponse::whereIn('registration_id', $regIds)->where('stage', 'post')->avg('total_score');

                return [
                    'id'           => $event->id,    
                    'name'         => $event->title ?? $event->name,
                    'category'     => ucfirst($event->category),
                    'participants' => $impact['attendees_count'],
                    'pre_avg'      => $preAvg !== null ? round((float) $preAvg, 1) : '—',
                    'post_avg'     => $postAvg !== null ? round((float) $postAvg, 1) : '—',
                    'lift'         => ($impact['average_lift'] > 0 ? '+' : '') . $impact['average_lift'],
                    'survey_status' => ($preAvg !== null && $postAvg !== null) ? '✓' : '—',
                ];
            })->toArray();
    }
}
