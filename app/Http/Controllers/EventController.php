<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');

        $upcomingEvents = Event::where('starts_at', '>=', now()->startOfDay())
            ->when($category, fn ($q) => $q->where('category', $category))
            ->withCount('registrations')
            ->orderBy('starts_at', 'asc')
            ->get();

        $pastEvents = Event::where('starts_at', '<', now()->startOfDay())
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
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:physical,mental,financial,social'],
            'starts_at' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        $data['created_by'] = $request->user()->id;
        Event::create($data);

        return redirect()->route('events.management')->with('success', 'Event published successfully!');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();
        return redirect()->route('events.management')->with('success', 'Event deleted.');
    }

    public function eventmanagement(): View
    {
        // Fetch all events for the management dashboard
        $events = Event::with('createdBy')->orderByDesc('starts_at')->get();
        return view('hrview.hrdb_eventsmanagement', compact('events'));
    }
}