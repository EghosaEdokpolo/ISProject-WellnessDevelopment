<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use App\Models\Waiver;
use App\Models\WellbeingResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 15);
        $cutoff           = now($tz)->subMinutes($lateGraceMinutes);

        $category   = $request->query('category');
        $search     = trim((string) $request->query('search', ''));
        $pastFilter = (string) $request->query('past_filter', 'all');

        $applyFilters = function ($q) use ($category,$search): void {
            if ($category &&$category !== 'all') {
                $q->where('category',$category);
            }
            if ($search !== '') {$q->where(function ($sub) use ($search): void {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }
        };

        $upcomingEvents = Event::withCount('registrations')
            ->where('starts_at', '>', $cutoff)
            ->tap($applyFilters)
            ->orderBy('starts_at', 'asc')
            ->get();

        $myRegistrations = Registration::with(['checkIn', 'feedback', 'waiver'])
            ->where('user_id', $request->user()->id)
            ->get()
            ->keyBy('event_id');

        $pastQuery = Event::withCount('registrations')
            ->where(function ($q) use ($cutoff): void {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=',$cutoff);
            })
            ->tap($applyFilters);

        if ($pastFilter === 'mine') {
            $pastQuery->whereIn('id',$myRegistrations->keys());
        }

        $pastEvents =$pastQuery
            ->orderByDesc('starts_at')
            ->paginate(6, ['*'], 'past_page')
            ->withQueryString();

        return view('events.index', [
            'events'          => $upcomingEvents,
            'upcomingEvents'  => $upcomingEvents,
            'pastEvents'      => $pastEvents,
            'myRegistrations' => $myRegistrations,
            'activeCategory'  => $category ?: 'all',
            'search'          => $search,
            'pastFilter'      => $pastFilter,
        ]);
    }

    public function management(Request $request): View
    {
        abort_unless($request->user()?->isHrCoordinator(), 403, 'Unauthorized access to Event Management.');

        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 15);$cutoff           = now($tz)->subMinutes($lateGraceMinutes);

        $allDbEvents = Event::withCount(['registrations', 'checkIns'])
            ->orderByDesc('starts_at')
            ->get();

        $upcomingEvents =$allDbEvents
            ->filter(fn (Event $e): bool => $e->starts_at !== null &&$e->starts_at->copy()->shiftTimezone($tz)->gt($cutoff))
            ->sortBy(fn (Event $e) =>$e->starts_at->timestamp)
            ->values();

        $pastEvents =$allDbEvents
            ->filter(fn (Event $e): bool => $e->starts_at === null ||$e->starts_at->copy()->shiftTimezone($tz)->lte($cutoff))
            ->sortByDesc(fn (Event $e) =>$e->starts_at?->timestamp ?? 0)
            ->values();

        $events = $upcomingEvents->concat($pastEvents)->values();

        return view('hrview.hrdb_eventsmanagement', [
            'events'         => $events,
            'upcomingEvents' => $upcomingEvents,
            'pastEvents'     => $pastEvents,
            'cutoff'         => $cutoff,
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        return view('events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        $whatsappUrl = $this->normalizeWhatsappUrl($request->input('whatsapp_channel_url'));
        if ($whatsappUrl !== null) {
            $request->merge(['whatsapp_channel_url' =>$whatsappUrl]);
        }

        $data =$request->validate([
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
        $parsedTime = Carbon::parse($data['starts_at'], $tz);$startsAt   = $parsedTime->lt($nowEat) ? $nowEat :$parsedTime;

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
            $payload['whatsapp_channel_url'] =$whatsappUrl;
        }

        $createdEvent = Event::create($payload);
        $this->clearAnalyticsCaches($createdEvent->category);

        return redirect()
            ->route('events.management')
            ->with('qr_event_id', $createdEvent->id)
            ->with('success', 'Wellness session "' . $data['name'] . '" published for ' . $startsAt->format('D, d M Y \a\t h:i A') . ' EAT! Its venue QR code is ready to download below.');
    }

    public function edit(Request $request, Event$event): View
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        return view('hrview.hrdb_eventsedit', [
            'event'  => $event,
            'isPast' => $this->isPastEvent($event),
        ]);
    }

    public function update(Request $request, Event$event): RedirectResponse
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        $whatsappUrl = $this->normalizeWhatsappUrl($request->input('whatsapp_channel_url'));
        if ($whatsappUrl !== null) {
            $request->merge(['whatsapp_channel_url' =>$whatsappUrl]);
        }

        $isPast = $this->isPastEvent($event);

        if ($isPast) {
            $data =$request->validate([
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
                $payload['whatsapp_channel_url'] =$whatsappUrl;
            }

            $event->update($payload);
            $this->clearAnalyticsCaches($event->category);

            return redirect()
                ->route('events.management')
                ->with('success', 'Past event details updated for "' . $event->name . '". Critical audit fields (date, capacity, pillar, and points) remain locked.');
        }

        $data =$request->validate([
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
        $parsedTime = Carbon::parse($data['starts_at'], $tz);$startsAt   = $parsedTime->lt($nowEat) ? $nowEat :$parsedTime;

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
            $payload['whatsapp_channel_url'] =$whatsappUrl;
        }

        $event->update($payload);
        $this->clearAnalyticsCaches($event->category);

        return redirect()
            ->route('events.management')
            ->with('qr_event_id', $event->id)
            ->with('success', 'Wellness session "' . $event->name . '" updated for ' . $startsAt->format('D, d M Y \a\t h:i A') . ' EAT.');
    }

    /**
     * Audit-guarded delete:
     * - Permits deleting unattended / draft / test sessions (0 verified check-ins).
     * - Blocks deleting attended sessions to preserve audit trails and KDPA 2019 retention compliance.
     */
    public function destroy(Request $request, Event $event): RedirectResponse
    {
        abort_unless($request->user()?->isHrCoordinator(), 403);

        $checkedInCount = $event->registrations()
            ->where(fn ($q) => $q->whereIn('status', ['checked_in', 'completed'])->orWhereHas('checkIn'))
            ->count();

        if ($checkedInCount > 0) {
            return redirect()
                ->route('events.edit', $event)
                ->withErrors([
                    'event' => "Cannot delete \"{$event->name}\" because {$checkedInCount} staff member(s) already have verified attendance records. Under Strathmore audit & KDPA 2019 retention rules, attended sessions must be retained for 1 academic year.",
                ]);
        }

        $eventName = $event->name;
        $category  = $event->category;

        DB::transaction(function () use ($event): void {
            $regIds          = $event->registrations()->pluck('id');
            $affectedUserIds = $event->registrations()->pluck('user_id')->unique();

            if ($regIds->isNotEmpty()) {
                PointsTransaction::whereIn('registration_id', $regIds)->delete();
                Waiver::whereIn('registration_id', $regIds)->delete();
                WellbeingResponse::whereIn('registration_id', $regIds)->delete();
                Registration::whereIn('id', $regIds)->delete();
            }

            $event->delete();

            foreach ($affectedUserIds as $userId) {
                $recalculated = max(0, (int) PointsTransaction::where('user_id', $userId)->sum('points'));
                User::where('id', $userId)->update(['points_balance' => $recalculated]);
            }
        });

        $this->clearAnalyticsCaches($category);

        return redirect()
            ->route('events.management')
            ->with('success', 'Unattended session "' . $eventName . '" was deleted cleanly.');
    }

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

    private function isPastEvent(Event $event): bool
    {
        if (! $event->starts_at) {
            return true;
        }

        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 15);
        $cutoff           = now($tz)->subMinutes($lateGraceMinutes);

        return $event->starts_at->copy()->shiftTimezone($tz)->lte($cutoff);
    }

    private function clearAnalyticsCaches(?string $category = null): void
    {
        Cache::forget((string) config('wellloop.cache.hr_overview_key', 'hr_overview_data_v21'));
        $prefix = (string) config('wellloop.cache.hr_impact_prefix', 'hr_impact_data_v6_');
        Cache::forget($prefix);
        if ($category) {
            Cache::forget($prefix . $category);
        }
    }
}