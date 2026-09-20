<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Staff;

class DashboardService
{
    public function __construct(
        private WellnessImpactService        $impact,
        private RetentionService             $retention,
        private DepartmentalEngagementService $departmental,
    ) {}

    public function overview(): array
    {
        $impact    = $this->impact->categoryBreakdown();
        $retention = $this->retention->sustainedParticipation();
        $dept      = $this->departmental->ranking();

        $avgLift = collect($impact)->avg('avg_lift') ?: 0;

        return [
            'leading' => [
                'events_published'    => Event::where('is_published', true)->count(),
                'total_registrations' => EventAttendance::count(),
                'staff_with_consent'  => Staff::where('consent_given', true)->count(),
                'system_adoption_pct' => $this->adoptionRate(),
            ],
            'lagging' => [
                'avg_impact_lift'           => round($avgLift, 2),
                'sustained_participation_pct' => $retention['sustained_participation'],
                'avg_points_per_staff'      => round((float) Staff::avg('points_balance'), 1),
            ],
            'departmental_snapshot' => [
                'top'    => array_slice($dept['most_active'], 0, 3),
                'bottom' => array_slice($dept['needs_attention'], 0, 3),
            ],
        ];
    }

    protected function adoptionRate(): float
    {
        $total = Staff::where('is_active', true)->count();
        if ($total === 0) return 0.0;

        $active = Staff::where('is_active', true)
            ->where(function ($q) {
                $q->whereHas('attendance')
                  ->orWhereHas('assessments')
                  ->orWhereHas('feedback');
            })
            ->count();

        return round(($active / $total) * 100, 1);
    }
}