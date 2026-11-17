<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class EventController extends Controller
{
    private const CATEGORIES = [
        'physical',
        'mental',
        'financial',
        'social',
        'intellectual',
        'occupational',
        'environmental',
        'spiritual',
    ];

    public function index(Request $request): View
    {
        $category = $request->query('category');
        $userId = auth()->id();

        $upcomingEvents = Event::where('starts_at', '>=', now()->startOfDay())
            ->when($category, fn ($q) => $q->where('category', $category))
            ->withCount('registrations')
            ->with(['registrations' => fn ($q) => $q->where('user_id', $userId)])
            ->orderBy('starts_at', 'asc')
            ->get();

        $pastEvents = Event::where('starts_at', '<', now()->startOfDay())
            ->when($category, fn ($q) => $q->where('category', $category))
            ->withCount('registrations')
            ->with(['registrations' => fn ($q) => $q->where('user_id', $userId)])
            ->orderByDesc('starts_at')
            ->limit(6)
            ->get();

        $latestArticles = Content::where('is_published', true)->latest()->take(3)->get();

        return view('events.index', [
            'events'         => $upcomingEvents,
            'pastEvents'     => $pastEvents,
            'activeCategory' => $category,
            'latestArticles' => $latestArticles,
        ]);
    }

    public function create(): View
    {
        $myEvents = Event::withCount('registrations')->orderByDesc('starts_at')->get();

        return view('events.create', compact('myEvents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'category'        => ['required', 'string', 'in:' . implode(',', self::CATEGORIES)],
            'starts_at'       => ['required', 'date'],
            'location'        => ['required', 'string', 'max:255'],
            'capacity'        => ['required', 'integer', 'min:1'],
            'description'     => ['nullable', 'string'],
            'comlink'         => ['nullable', 'url', 'max:500'],
            'points_checkin'  => ['required', 'integer', 'min:0'],
            'points_feedback' => ['required', 'integer', 'min:0'],
        ]);

        $data['created_by'] = $request->user()->id;
        $event = Event::create($data);

        $this->clearAnalyticsCache();

        $isPast = Carbon::parse($event->starts_at)->isPast();
        $message = $isPast
            ? 'Historical past event logged successfully! You can now record attendee data via the Attendee Table.'
            : 'Upcoming event published successfully!';

        return redirect()->route('events.management')->with('success', $message);
    }

    public function edit(Event $event): View
    {
        $event->loadCount('registrations');

        return view('hrview.hrdb_eventsedit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $isPast = $event->starts_at->isPast();

        $data = $request->validate(
            [
                'name'            => ['required', 'string', 'max:255'],
                'category'        => ['required', 'string', 'in:' . implode(',', self::CATEGORIES)],
                'starts_at'       => $isPast ? ['nullable', 'date'] : ['required', 'date', 'after:now'],
                'location'        => ['required', 'string', 'max:255'],
                'capacity'        => ['required', 'integer', 'min:1'],
                'description'     => ['nullable', 'string'],
                'comlink'         => ['nullable', 'url', 'max:500'],
                'points_checkin'  => ['required', 'integer', 'min:0'],
                'points_feedback' => ['required', 'integer', 'min:0'],
            ],
            [
                'starts_at.after' => 'An upcoming event cannot be moved backward into the past. Please select a future date and time.',
            ]
        );

        // Server-side audit lock: preserve historical date, pillar category, and awarded points on past events
        if ($isPast) {
            $event->update([
                'name'        => $data['name'],
                'capacity'    => max((int) $data['capacity'], $event->registrations()->count()),
                'location'    => $data['location'],
                'description' => $data['description'],
                'comlink'     => $data['comlink'],
            ]);

            $this->clearAnalyticsCache();

            return redirect()
                ->route('events.management')
                ->with('success', 'Historical event metadata updated! Date, category, and point values remain audit-locked.');
        }

        $event->update($data);

        $this->clearAnalyticsCache();

        return redirect()->route('events.management')->with('success', 'Upcoming event updated successfully!');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->starts_at->isPast() && $event->registrations()->exists()) {
            return redirect()
                ->route('events.management')
                ->withErrors(['audit' => 'Cannot delete a past event that already has staff attendance records.']);
        }

        $event->delete();
        $this->clearAnalyticsCache();

        return redirect()->route('events.management')->with('success', 'Event deleted.');
    }

    public function eventmanagement(): View
    {
        $events = Event::with('createdBy')
            ->withCount('registrations')
            ->orderByDesc('starts_at')
            ->get();

        return view('hrview.hrdb_eventsmanagement', compact('events'));
    }

    private function clearAnalyticsCache(): void
    {
        Cache::forget('hr_overview_data_v21');
        Cache::forget('hr_overview_data_v20');
        Cache::forget('hr_overview_data_v8');
        Cache::forget('hr_impact_data_v6_');

        foreach (self::CATEGORIES as $cat) {
            Cache::forget('hr_impact_data_v6_' . $cat);
        }
    }
}