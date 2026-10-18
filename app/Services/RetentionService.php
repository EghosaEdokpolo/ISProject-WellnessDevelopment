<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class RetentionService
{
    public function sustainedParticipation(?Carbon $start = null, ?Carbon $end = null): array
    {
        $start ??= self::academicYearStart(); $end ??= self::academicYearEnd();
        $totalActive = User::where('role', 'staff')->count();

        $sustained = User::where('role', 'staff')->whereHas('registrations', function ($q) use ($start, $end) {
            $q->where('status', 'completed')->whereBetween('created_at', [$start, $end]);
        }, '>=', 2)->count();

        return [
            'total_active_staff' => $totalActive, 'sustained_attendees' => $sustained,
            'sustained_participation' => $totalActive > 0 ? round(($sustained / $totalActive) * 100, 1) : 0,
            'period' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
        ];
    }

    public static function academicYearStart(): Carbon
    {
        $month = (int) now()->format('n'); $year = (int) now()->format('Y');
        return $month >= 9 ? Carbon::create($year, 9, 1)->startOfDay() : Carbon::create($year - 1, 9, 1)->startOfDay();
    }

    public static function academicYearEnd(): Carbon
    {
        return self::academicYearStart()->copy()->addYear()->subDay()->endOfDay();
    }
}