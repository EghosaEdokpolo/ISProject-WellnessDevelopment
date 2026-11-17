<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Event;
use App\Models\Feedback;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use App\Models\WellbeingResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HrDashboardService
{
    public function getOverviewData(): array
    {
        $data = Cache::remember('hr_overview_data_v21', 300, function (): array {
            $totalStaff = User::where('role', 'staff')->count();
            $registeredStaff = User::where('role', 'staff')->whereHas('registrations')->count();

            $sustainedStaffCount = User::where('role', 'staff')
                ->whereHas('registrations', fn ($q) => $q->whereIn('status', ['checked_in', 'completed']), '>=', 2)
                ->count();

            $postSurveys = WellbeingResponse::with('registration.event')
                ->where('stage', 'post')
                ->get();

            $fastSurveys = 0;
            foreach ($postSurveys as $survey) {
                if ($survey->registration && $survey->registration->event && $survey->registration->event->starts_at) {
                    $hoursDiff = $survey->created_at->diffInHours($survey->registration->event->starts_at);
                    if ($hoursDiff <= 24) {
                        $fastSurveys++;
                    }
                }
            }
            $surveyVelocityRate = $postSurveys->count() > 0
                ? round(($fastSurveys / $postSurveys->count()) * 100, 1)
                : 0.0;

            $totalRegistrations = Registration::count();
            $consented = Registration::where('consent_given', true)->count();
            $waiversSigned = Registration::whereHas('waiver')->count();

            $consentRate = $totalRegistrations > 0 ? round(($consented / $totalRegistrations) * 100, 1) : 0.0;
            $waiverRate = $totalRegistrations > 0 ? round(($waiversSigned / $totalRegistrations) * 100, 1) : 0.0;

            $avgLift = round(
                (WellbeingResponse::where('stage', 'post')->avg('total_score') ?? 0)
                - (WellbeingResponse::where('stage', 'pre')->avg('total_score') ?? 0),
                1
            );

            $totalPointsEarned = (int) (PointsTransaction::where('points', '>', 0)->sum('points') ?: User::where('role', 'staff')->sum('points_balance'));

            $categories = [
                'physical',
                'mental',
                'financial',
                'social',
                'intellectual',
                'occupational',
                'environmental',
                'spiritual',
            ];
            $chartData = [];

            foreach ($categories as $cat) {
                $catName = ucfirst($cat);
                $regCount = Registration::whereHas('event', fn ($q) => $q->where('category', $cat))->count();
                $attCount = Registration::whereHas('event', fn ($q) => $q->where('category', $cat))
                    ->whereIn('status', ['checked_in', 'completed'])
                    ->count();

                $chartData["reg{$catName}"] = $regCount;
                $chartData["att{$catName}"] = $attCount;
            }

            $recentActivities = Registration::with(['user', 'event'])
                ->latest('updated_at')
                ->take(6)
                ->get()
                ->map(function (Registration $reg): array {
                    $marker = 'marker-gold';
                    $text = '<strong>' . e($reg->user->name ?? 'Unknown') . '</strong> registered for <strong>' . e($reg->event->name ?? 'Event') . '</strong>';

                    if ($reg->status === 'completed') {
                        $marker = 'marker-green';
                        $text = '<strong>' . e($reg->user->name ?? 'Unknown') . '</strong> completed feedback for <strong>' . e($reg->event->name ?? 'Event') . '</strong>';
                    } elseif ($reg->status === 'checked_in') {
                        $marker = 'marker-blue';
                        $text = '<strong>' . e($reg->user->name ?? 'Unknown') . '</strong> attended <strong>' . e($reg->event->name ?? 'Event') . '</strong>';
                    }

                    return [
                        'text'   => $text,
                        'time'   => $reg->updated_at ? $reg->updated_at->diffForHumans() : 'Recently',
                        'marker' => $marker,
                    ];
                })->toArray();

            $topEvent = Event::withCount([
                'registrations as total_registered',
                'registrations as total_attended' => fn ($q) => $q->whereIn('status', ['checked_in', 'completed']),
            ])
                ->orderByDesc('total_attended')
                ->orderByDesc('total_registered')
                ->first();

            $mostAttendedEvent = null;
            if ($topEvent && ($topEvent->total_attended > 0 || $topEvent->total_registered > 0)) {
                $mostAttendedEvent = [
                    'name'       => $topEvent->name,
                    'total'      => (int) $topEvent->total_attended,
                    'registered' => (int) $topEvent->total_registered,
                ];
            }

            $qualitativeFeedback = Feedback::with(['registration.user', 'registration.event'])
                ->whereNotNull('comments')
                ->where('comments', '!=', '')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (Feedback $fb): array => [
                    'comment'    => $fb->comments,
                    'rating'     => (int) ($fb->satisfaction_stars ?? 5),
                    'user_name'  => optional(optional($fb->registration)->user)->name ?? 'Staff Member',
                    'event_name' => optional(optional($fb->registration)->event)->name ?? 'Wellness Event',
                ])->toArray();

            return array_merge([
                'registrationRate'           => $totalStaff > 0 ? round(($registeredStaff / $totalStaff) * 100, 1) : 0.0,
                'sustainedParticipationRate' => $totalStaff > 0 ? round(($sustainedStaffCount / $totalStaff) * 100, 1) : 0.0,
                'consentRate'                => $consentRate,
                'consentCompletionRate'      => $consentRate,
                'surveyVelocityRate'         => $surveyVelocityRate,
                'waiverRate'                 => $waiverRate,
                'avgLift'                    => max(0.0, $avgLift),
                'registeredStaff'            => $registeredStaff,
                'totalStaff'                 => $totalStaff,
                'totalPointsEarned'          => $totalPointsEarned,
                'recentActivities'           => $recentActivities,
                'mostAttendedEvent'          => $mostAttendedEvent,
                'qualitativeFeedback'        => $qualitativeFeedback,
            ], $chartData);
        });

        $totalStaffCount = max(1, (int) ($data['totalStaff'] ?? 1));

        $data['eventsBreakdown'] = Event::withCount([
            'registrations as total_registered',
            'registrations as total_attended' => fn ($q) => $q->whereIn('status', ['checked_in', 'completed']),
        ])
            ->orderByDesc('starts_at')
            ->take(12)
            ->get()
            ->map(function (Event $event) use ($totalStaffCount): Event {
                $denominator = (int) ($event->capacity ?? 0) > 0
                    ? (int) $event->capacity
                    : $totalStaffCount;

                $event->registration_rate = min(
                    100.0,
                    round(((int) $event->total_registered / $denominator) * 100, 1)
                );

                $regIds = Registration::where('event_id', $event->id)->pluck('id');
                $event->points_transactions_sum_points = $regIds->isEmpty()
                    ? 0
                    : (int) PointsTransaction::whereIn('registration_id', $regIds)
                        ->where('points', '>', 0)
                        ->sum('points');

                return $event;
            });

        return $data;
    }

    public function getAttendees(Request $request): LengthAwarePaginator
    {
        $query = Registration::with(['user', 'event', 'waiver', 'checkIn', 'feedback', 'wellbeingResponses']);

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search): void {
                $q->where(fn ($sub) => $sub
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%"));
            });
        }

        return $query->latest()->paginate(15)->withQueryString();
    }
}