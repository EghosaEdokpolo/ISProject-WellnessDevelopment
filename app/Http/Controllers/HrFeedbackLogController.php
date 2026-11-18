<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HrFeedbackLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()?->isHrCoordinator(), 403, 'Unauthorized access to HR feedback logs.');

        $tz      = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $eventId = $request->integer('event_id');
        $search  = trim((string) $request->query('search', ''));

        $baseQuery = Registration::with(['user', 'event', 'feedback'])
            ->whereHas('feedback');

        if ($eventId > 0) {
            $baseQuery->where('event_id', $eventId);
        }

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search): void {
                $q->whereHas('user', function ($uq) use ($search): void {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('staff_id', 'like', "%{$search}%")
                       ->orWhere('department', 'like', "%{$search}%");
                })->orWhereHas('event', function ($eq) use ($search): void {
                    $eq->where('name', 'like', "%{$search}%");
                })->orWhereHas('feedback', function ($fq) use ($search): void {
                    $fq->where('comment', 'like', "%{$search}%");
                });
            });
        }

        $allMatching = (clone $baseQuery)->get();

        $totalCount = $allMatching->count();
        $avgRating  = $totalCount > 0
            ? round((float) $allMatching->avg(fn (Registration $r) => (float) ($r->feedback->rating ?? 5)), 1)
            : 0.0;

        $logs = $baseQuery
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        $events = Event::orderByDesc('starts_at')->get(['id', 'name', 'starts_at']);

        return view('hrview.hrdb_feedback_logs', [
            'logs'            => $logs,
            'events'          => $events,
            'selectedEventId' => $eventId,
            'search'          => $search,
            'totalCount'      => $totalCount,
            'avgRating'       => $avgRating,
            'tz'              => $tz,
        ]);
    }
}