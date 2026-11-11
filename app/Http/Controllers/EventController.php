<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');
        $upcomingEvents = Event::where('starts_at', '>=', now())
            ->when($category, fn ($q) => $q->where('category', $category))
            ->withCount('registrations')
            ->orderBy('starts_at')
            ->get();

        $pastEvents = Event::where('starts_at', '<', now())
            ->when($category, fn ($q) => $q->where('category', $category))
            ->withCount('registrations')
            ->orderByDesc('starts_at')
            ->limit(6)
            ->get();

        return view('events.index', [
            'events' => $upcomingEvents,
            'pastEvents' => $pastEvents,
            'activeCategory' => $category,
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
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'in:physical,mental,financial,social,occupational,intellectual,spiritual,environmental'],
            'starts_at'   => ['required', 'date'],
            'location'    => ['required', 'string', 'max:255'],
            'capacity'    => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'points_checkin'  => ['required', 'integer', 'min:0'],
            'points_feedback' => ['required', 'integer', 'min:0'],
            'comlink' => ['nullable', 'url', 'max:500'],

        ]);

        $data['created_by'] = $request->user()->id;
        Event::create($data);

        return redirect()->route('events.management')->with('success', 'Event published!');
    }

    /**
     * Show the prefilled edit form for a specific event.
     */
    public function edit(Event $event): View
    {
        return view('hrview.hrdb_eventsedit', compact('event'));
    }

    /**
     * Validate and save changes to the database.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'eventName'               => ['required', 'string', 'max:255'],
            'date'                    => ['required', 'date'],
            'location'                => ['required', 'string', 'max:255'],
            'wellcat'                 => ['required', 'in:physical,mental,financial,social,occupational,intellectual,spiritual,environmental'],
            'description'             => ['required', 'string'],
            'points_awarded_checkin'  => ['required', 'integer', 'min:0'],
            'points_awarded_feedback' => ['required', 'integer', 'min:0'],
            'comlink'                 => ['nullable', 'url', 'max:500'],

        ]);

        $event->update([
            'name'            => $validated['eventName'],
            'starts_at'       => $validated['date'],
            'location'        => $validated['location'],
            'category'        => $validated['wellcat'],
            'description'     => $validated['description'],
            'comlink'         => $validated['comlink'] ?? null,
            'points_checkin'  => $validated['points_awarded_checkin'],
            'points_feedback' => $validated['points_awarded_feedback'],
        ]);

        return redirect()->route('events.management')->with('success', 'Event updated successfully!');
    }

    /**
     * Permanently delete an event from the database.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();
        return redirect()->route('events.management')->with('success', 'Event deleted.');
    }
}
