<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Models\WellbeingResponse;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HrOverviewService
{
    public function getDashboardData(): array
    {
        return Cache::remember('hr_overview_v3', 300, function () {
            
            $activeStaff = User::where('role', 'staff')->count();
            $registeredStaff = User::where('role', 'staff')->whereHas('registrations')->count();
            
            $start = now()->month >= 9 ? Carbon::create(now()->year, 9, 1) : Carbon::create(now()->year - 1, 9, 1);
            $end = $start->copy()->addYear()->subDay();

            $totalPoints = User::where('role', 'staff')->sum('points_balance') ?? 0;
            
            $avgLift = round(
                (WellbeingResponse::where('stage', 'post')->avg('total_score') ?? 0) - 
                (WellbeingResponse::where('stage', 'pre')->avg('total_score') ?? 0), 
                1
            );

            $categoryStats = Registration::whereBetween('created_at', [$start, $end])
                ->join('events', 'registrations.event_id', '=', 'events.id')
                ->select('events.category', DB::raw('count(*) as total'))
                ->groupBy('events.category')
                ->pluck('total', 'category')
                ->toArray();

            $recentActivities = Registration::with(['user', 'event'])
                ->latest('updated_at')
                ->take(5)
                ->get()
                ->map(function ($reg) {
                    $marker = 'marker-gold';
                    $text = "<strong>" . e(optional($reg->user)->name) . "</strong> registered for <strong>" . e(optional($reg->event)->name) . "</strong>";
                    
                    if ($reg->status === 'completed') {
                        $marker = 'marker-green';
                        $text = "<strong>" . e(optional($reg->user)->name) . "</strong> submitted wellness feedback";
                    } elseif ($reg->status === 'checked_in') {
                        $marker = 'marker-blue';
                        $text = "<strong>" . e(optional($reg->user)->name) . "</strong> checked in via QR code";
                    }
                    
                    return ['text' => $text, 'time' => $reg->updated_at->diffForHumans(), 'marker' => $marker];
                });

            $topEvent = Registration::with('event')
                ->selectRaw('event_id, count(*) as total')
                ->groupBy('event_id')
                ->orderByDesc('total')
                ->first();

            $recentFeedback = Feedback::with(['registration.user', 'registration.event'])
                ->whereNotNull('comments')
                ->where('comments', '!=', '')
                ->latest()
                ->take(4)
                ->get();

            return [
                'stats' => [
                    'activeStaff' => $activeStaff,
                    'registeredStaff' => $registeredStaff,
                    'registrationRate' => $activeStaff > 0 ? round(($registeredStaff / $activeStaff) * 100, 1) : 0,
                    'totalPoints' => $totalPoints,
                    'avgLift' => max(0, $avgLift),
                ],
                'categoryStats' => [
                    'physical' => $categoryStats['physical'] ?? 0,
                    'mental' => $categoryStats['mental'] ?? 0,
                    'financial' => $categoryStats['financial'] ?? 0,
                    'social' => $categoryStats['social'] ?? 0,
                ],
                'recentActivities' => $recentActivities,
                'topEvent' => $topEvent,
                'recentFeedback' => $recentFeedback,
            ];
        });
    }
}