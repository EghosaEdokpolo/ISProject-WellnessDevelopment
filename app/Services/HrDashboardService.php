<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use App\Models\Feedback;
use App\Models\Registration;
use App\Models\User;
use App\Models\WellbeingResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HrDashboardService
{
    public function getOverviewData(): array
    {
        return Cache::remember('hr_overview_data_v8', 300, function () {
            $totalStaff = User::where('role', 'staff')->count();
            $registeredStaff = User::where('role', 'staff')->whereHas('registrations')->count();
            
            $sustainedStaffCount = User::where('role', 'staff')
                ->whereHas('registrations', fn ($q) => $q->where('status', 'completed'), '>=', 2)
                ->count();

            $totalRegistrations = Registration::count();
            $consented = Registration::where('consent_given', true)->count();
            $waiversSigned = Registration::whereHas('waiver')->count();
            
            $consentRate = $totalRegistrations > 0 ? round(($consented / $totalRegistrations) * 100, 1) : 0;
            $waiverRate = $totalRegistrations > 0 ? round(($waiversSigned / $totalRegistrations) * 100, 1) : 0;

            $avgLift = round(
                (WellbeingResponse::where('stage', 'post')->avg('total_score') ?? 0)
                - (WellbeingResponse::where('stage', 'pre')->avg('total_score') ?? 0),
                1
            );

            $totalPointsEarned = User::where('role', 'staff')->sum('points_balance') ?? 0;

            $categoryStats = Registration::join('events', 'registrations.event_id', '=', 'events.id')
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
                    $text = "<strong>" . e($reg->user->name ?? 'Unknown') . "</strong> registered for <strong>" . e($reg->event->name ?? 'Event') . "</strong>";
                    if ($reg->status === 'completed') {
                        $marker = 'marker-green';
                        $text = "<strong>" . e($reg->user->name ?? 'Unknown') . "</strong> submitted wellness feedback";
                    } elseif ($reg->status === 'checked_in') {
                        $marker = 'marker-blue';
                        $text = "<strong>" . e($reg->user->name ?? 'Unknown') . "</strong> checked in via QR code";
                    }
                    return ['text' => $text, 'time' => $reg->updated_at->diffForHumans(), 'marker' => $marker];
                })->toArray();

            $topEvent = Registration::with('event')->selectRaw('event_id, count(*) as total')->groupBy('event_id')->orderByDesc('total')->first();
            $mostAttendedEvent = $topEvent ? ['name' => optional($topEvent->event)->name ?? 'No data', 'total' => $topEvent->total] : null;

            $qualitativeFeedback = Feedback::with(['registration.user', 'registration.event'])
                ->whereNotNull('comments')->where('comments', '!=', '')->latest()->take(5)->get()
                ->map(fn($fb) => [
                    'comment' => $fb->comments,
                    'user_name' => optional($fb->registration->user)->name ?? 'Unknown',
                    'event_name' => optional($fb->registration->event)->name ?? 'Unknown'
                ])->toArray();

            return [
                'registrationRate' => $totalStaff ? round($registeredStaff / $totalStaff * 100, 1) : 0,
                'sustainedParticipationRate' => $totalStaff ? round($sustainedStaffCount / $totalStaff * 100, 1) : 0,
                'consentRate' => $consentRate,
                'waiverRate' => $waiverRate,
                'avgLift' => max(0, $avgLift),
                'registeredStaff' => $registeredStaff,
                'totalStaff' => $totalStaff,
                'totalPointsEarned' => $totalPointsEarned,
                'physicalCount' => $categoryStats['physical'] ?? 0,
                'mentalCount' => $categoryStats['mental'] ?? 0,
                'financialCount' => $categoryStats['financial'] ?? 0,
                'socialCount' => $categoryStats['social'] ?? 0,
                'recentActivities' => $recentActivities,
                'mostAttendedEvent' => $mostAttendedEvent,
                'qualitativeFeedback' => $qualitativeFeedback,
            ];
        });
    }

    public function getAttendees(Request $request): LengthAwarePaginator
    {
        $query = Registration::with(['user', 'event', 'waiver', 'checkIn', 'feedback', 'wellbeingResponses']);

        // Filter by Event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by Name, Staff ID, or Employee ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                // Wrapping in a closure ensures the OR conditions don't break other filters
                $q->where(function($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('staff_id', 'like', "%{$search}%")
                             ->orWhere('employee_id', 'like', "%{$search}%");
                });
            });
        }

        return $query->latest()->paginate(15)->withQueryString();
    }
}