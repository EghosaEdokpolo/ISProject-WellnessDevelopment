<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display the Staff Wellness Events Hub (separated into Upcoming & Past Sessions).
     */
    public function index(Request $request): View
    {
        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 10);
        $cutoff           = now($tz)->subMinutes($lateGraceMinutes);

        $category = $request->query('category');
        $search   = trim((string) $request->query('search', ''));

        $baseQuery = Event::withCount('registrations');

        if ($category && $category !== 'all') {
            $baseQuery->where('category', $category);
        }

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allMatching = $baseQuery->orderBy('starts_at', 'asc')->get();

        $upcomingEvents = $allMatching
            ->filter(fn (Event $e): bool => $e->starts_at !== null && $e->starts_at->copy()->shiftTimezone($tz)->gt($cutoff))
            ->values();

        $pastEvents = $allMatching
            ->filter(fn (Event $e): bool => $e->starts_at === null || $e->starts_at->copy()->shiftTimezone($tz)->lte($cutoff))
            ->sortByDesc(fn (Event $e) => $e->starts_at?->timestamp ?? 0)
            ->values();

        $myRegistrations = Registration::with(['checkIn', 'feedback', 'waiver'])
            ->where('user_id', $request->user()->id)
            ->get()
            ->keyBy('event_id');

        return view('events.index', [
            'events'          => $upcomingEvents,
            'upcomingEvents'  => $upcomingEvents,
            'pastEvents'      => $pastEvents,
            'myRegistrations' => $myRegistrations,
            'activeCategory'  => $category ?: 'all',
            'search'          => $search,
        ]);
    }

    /**
     * Display the HR Coordinator Event Creation, Management & Inline QR Console with live DB records.
     */
    public function management(Request $request): View
    {
        abort_unless($request->user()?->isHrCoordinator(), 403, 'Unauthorized access to Event Management.');

        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 10);
        $cutoff           = now($tz)->subMinutes($lateGraceMinutes);

        $allDbEvents = Event::withCount(['registrations', 'checkIns'])
            ->orderByDesc('starts_at')
            ->get();

        $upcomingEvents = $allDbEvents
            ->filter(fn (Event $e): bool => $e->starts_at !== null && $e->starts_at->copy()->shiftTimezone($tz)->gt($cutoff))
            ->sortBy(fn (Event $e) => $e->starts_at->timestamp)
            ->values();

        $pastEvents = $allDbEvents
            ->filter(fn (Event $e): bool => $e->starts_at === null || $e->starts_at->copy()->shiftTimezone($tz)->lte($cutoff))
            ->sortByDesc(fn (Event $e) => $e->starts_at?->timestamp ?? 0)
            ->values();

        $events = $upcomingEvents->concat($pastEvents)->values();

        return view('hrview.hrdb_eventsmanagement', [
            'events'         => $events,
            'upcomingEvents' => $upcomingEvents,
            'pastEvents'     => $pastEvents,
            'cutoff'         => $cutoff,
        ]);
    }

    /**
     * Show standalone event creation form if accessed directly.
     */
    public function create(Request $request): View
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        return view('events.create');
    }

    /**
     * Store a newly scheduled Strathmore University wellness event and auto-select its inline QR code.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        $whatsappUrl = $this->normalizeWhatsappUrl($request->input('whatsapp_channel_url'));
        if ($whatsappUrl !== null) {
            $request->merge(['whatsapp_channel_url' => $whatsappUrl]);
        }

        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'description'          => ['nullable', 'string', 'max:2000'],
            'starts_at'            => ['required', 'date'],
            'location'             => ['required', 'string', 'max:255'],
            'whatsapp_channel_url' => ['nullable', 'url', 'max:500'],
            'category'             => ['required', 'string', 'max:50'],
            'capacity'             => ['required', 'integer', 'min:1', 'max:5000'],
            'points_checkin'       => ['required', 'integer', 'min:0', 'max:500'],
            'points_feedback'      => ['required', 'integer', 'min:0', 'max:500'],
        ]);

        $tz         = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $nowEat     = now($tz)->startOfMinute();
        $parsedTime = Carbon::parse($data['starts_at'], $tz);

        // Automatically clamp any past time to current EAT time
        $startsAt = $parsedTime->lt($nowEat) ? $nowEat : $parsedTime;

        $payload = [
            'created_by'      => $request->user()->id,
            'name'            => $data['name'],
            'description'     => $data['description'] ?? null,
            'starts_at'       => $startsAt,
            'location'        => $data['location'],
            'comlink'         => $whatsappUrl,
            'category'        => strtolower($data['category']),
            'capacity'        => (int) $data['capacity'],
            'points_checkin'  => (int) $data['points_checkin'],
            'points_feedback' => (int) $data['points_feedback'],
            'is_published'    => true,
            'published_at'    => now($tz),
        ];

        if (Schema::hasColumn('events', 'whatsapp_channel_url')) {
            $payload['whatsapp_channel_url'] = $whatsappUrl;
        }

        $createdEvent = Event::create($payload);
        $this->clearAnalyticsCaches();

        return redirect()
            ->route('events.management')
            ->with('qr_event_id', $createdEvent->id)
            ->with('success', 'Wellness session "' . $data['name'] . '" published for ' . $startsAt->format('D, d M Y \a\t h:i A') . ' EAT! Its venue QR code is ready to download below.');
    }

    /**
     * Show the edit form for both Upcoming and Past Events.
     * Past events open in Audit-Protected Mode (cosmetic text fields editable; critical audit fields locked).
     */
    public function edit(Request $request, Event $event): View
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        return view('hrview.hrdb_eventsedit', [
            'event'  => $event,
            'isPast' => $this->isPastEvent($event),
        ]);
    }

    /**
     * Update an existing event record.
     * - Past Event: Updates only cosmetic fields (name, location, description, whatsapp_channel_url).
     *   Critical audit fields (starts_at, capacity, category, points_checkin, points_feedback) remain locked.
     * - Upcoming Event: Updates all fields and clamps starts_at if set in the past.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        $whatsappUrl = $this->normalizeWhatsappUrl($request->input('whatsapp_channel_url'));
        if ($whatsappUrl !== null) {
            $request->merge(['whatsapp_channel_url' => $whatsappUrl]);
        }

        $isPast = $this->isPastEvent($event);

        if ($isPast) {
            $data = $request->validate([
                'name'                 => ['required', 'string', 'max:255'],
                'location'             => ['required', 'string', 'max:255'],
                'whatsapp_channel_url' => ['nullable', 'url', 'max:500'],
                'description'          => ['nullable', 'string', 'max:2000'],
            ]);

            $payload = [
                'name'        => $data['name'],
                'location'    => $data['location'],
                'comlink'     => $whatsappUrl,
                'description' => $data['description'] ?? null,
            ];

            if (Schema::hasColumn('events', 'whatsapp_channel_url')) {
                $payload['whatsapp_channel_url'] = $whatsappUrl;
            }

            $event->update($payload);
            $this->clearAnalyticsCaches();

            return redirect()
                ->route('events.management')
                ->with('success', 'Past event details updated for "' . $event->name . '". Critical audit fields (date, capacity, pillar, and points) remain locked.');
        }

        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'description'          => ['nullable', 'string', 'max:2000'],
            'starts_at'            => ['required', 'date'],
            'location'             => ['required', 'string', 'max:255'],
            'whatsapp_channel_url' => ['nullable', 'url', 'max:500'],
            'category'             => ['required', 'string', 'max:50'],
            'capacity'             => ['required', 'integer', 'min:1', 'max:5000'],
            'points_checkin'       => ['required', 'integer', 'min:0', 'max:500'],
            'points_feedback'      => ['required', 'integer', 'min:0', 'max:500'],
        ]);

        $tz         = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $nowEat     = now($tz)->startOfMinute();
        $parsedTime = Carbon::parse($data['starts_at'], $tz);
        $startsAt   = $parsedTime->lt($nowEat) ? $nowEat : $parsedTime;

        $payload = [
            'name'            => $data['name'],
            'description'     => $data['description'] ?? null,
            'starts_at'       => $startsAt,
            'location'        => $data['location'],
            'comlink'         => $whatsappUrl,
            'category'        => strtolower($data['category']),
            'capacity'        => (int) $data['capacity'],
            'points_checkin'  => (int) $data['points_checkin'],
            'points_feedback' => (int) $data['points_feedback'],
        ];

        if (Schema::hasColumn('events', 'whatsapp_channel_url')) {
            $payload['whatsapp_channel_url'] = $whatsappUrl;
        }

        $event->update($payload);
        $this->clearAnalyticsCaches();

        return redirect()
            ->route('events.management')
            ->with('qr_event_id', $event->id)
            ->with('success', 'Wellness session "' . $event->name . '" updated for ' . $startsAt->format('D, d M Y \a\t h:i A') . ' EAT.');
    }

    /**
     * Delete an event record.
     */
    public function destroy(Request $request, Event $event): RedirectResponse
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        $eventName = $event->name;
        $event->delete();
        $this->clearAnalyticsCaches();

        return redirect()
            ->route('events.management')
            ->with('success', 'Wellness session "' . $eventName . '" removed.');
    }

    /**
     * Normalize WhatsApp links so URLs pasted with or without https:// validate and save cleanly.
     */
    private function normalizeWhatsappUrl(mixed $raw): ?string
    {
        $trimmed = trim((string) ($raw ?? ''));
        if ($trimmed === '') {
            return null;
        }

        if (! preg_match('~^https?://~i', $trimmed)) {
            $trimmed = 'https://' . ltrim($trimmed, '/');
        }

        return $trimmed;
    }

    /**
     * Determine whether an event has passed its 10-minute check-in grace window.
     */
    private function isPastEvent(Event $event): bool
    {
        if (! $event->starts_at) {
            return true;
        }

        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 10);
        $cutoff           = now($tz)->subMinutes($lateGraceMinutes);

        return $event->starts_at->copy()->shiftTimezone($tz)->lte($cutoff);
    }

    /**
     * Invalidate cached HR dashboard KPIs after event mutations.
     */
    private function clearAnalyticsCaches(): void
    {
        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));
        Cache::forget((string) config('wellloop.cache.hr_impact_prefix', 'hr_impact_data_v6_'));
    }
}