<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    // Staff-facing: "Upcoming Wellness Events" grid + a Past Events section
    public function index(Request $request): View
    {
        $category = $request->query('category');

        $upcomingEvents = Event::where('starts_at', '>=', now())
            ->when($category, fn ($query) => $query->where('category', $category))
            ->withCount('registrations')
            ->orderBy('starts_at')
            ->get();

        // POINTER: same query shape as above but flipped to '<' and ordered
        // descending (most recent past event first) — this is what powers
        // the "Past Events" section so staff can see what they already did.
        $pastEvents = Event::where('starts_at', '<', now())
            ->when($category, fn ($query) => $query->where('category', $category))
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

    // HR-facing: "Create & Publish Event" form + list of existing events to manage
    public function create(): View
    {
        $myEvents = Event::withCount('registrations')->orderByDesc('starts_at')->get();

        return view('events.create', compact('myEvents'));
    }

    public function store(Request $request): RedirectResponse
    {
        // POINTER: validate() does two jobs at once — it rejects bad input
        // AND, on failure, automatically redirects back to the form with
        // the errors, which is why events.create can show $errors below.
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:physical,mental,financial,social'],
            'starts_at' => ['required', 'date', 'after:now'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        $data['created_by'] = $request->user()->id;

        Event::create($data);

        return redirect()->route('events.create')->with('status', 'Event published!');
    }

    // POINTER: Route::delete + this method is Laravel's standard "destroy"
    // pattern. Because the registrations table's event_id foreign key was
    // defined with ->cascadeOnDelete() back in the migration, deleting an
    // Event automatically deletes its registrations too — and THOSE cascade
    // to delete waivers/check-ins/feedback/wellbeing responses in turn. One
    // line here cleans up the whole chain; you don't delete each table by hand.
    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('events.create')->with('status', 'Event deleted.');
    }
}
