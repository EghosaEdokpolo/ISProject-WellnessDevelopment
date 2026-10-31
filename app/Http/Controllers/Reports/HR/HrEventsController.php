<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event; 

class HrEventsController extends Controller
{
    /**
     * 1. DISPLAY LOG LISTS
     */
    public function index()
    {
        // Fetch all upcoming system events ordered by date sequence
        $upcomingEvents = Event::orderBy('starts_at', 'desc')->get();
        
        return view('hrview.hrdb_eventsmanagement', compact('upcomingEvents'));
    }

    /**
     * 2. SAVE & PUBLISH NEW EVENT
     */
    public function store(Request $request)
    {
        // Enforce rule boundaries and prevent blank form anomalies
        $validated = $request->validate([
            'eventName'               => 'required|string|max:255',
            'date'                    => 'required',
            'location'                => 'required|string|max:255',
            'wellcat'                 => 'required|in:physical,mental,financial,social',
            'description'             => 'required|string',
            'points_awarded_checkin'  => 'required|integer|min:0',
            'points_awarded_feedback' => 'required|integer|min:0',
        ]);

        // Write the incoming form package live to your SQL server tables
        Event::create([
            'name'             => $validated['eventName'],
            'starts_at'        => $validated['date'],
            'location'         => $validated['location'],
            'category'         => $validated['wellcat'],
            'description'      => $validated['description'],
            'points_checkin'   => $validated['points_awarded_checkin'],
            'points_feedback'  => $validated['points_awarded_feedback'],
            'created_by'       => auth()->id() ?? 1, // Fallback placeholder ID matching your image column
        ]);

        return redirect()->back()->with('success', 'Event published successfully!');
    }

    /**
     * 3. WIPE OUT / DELETE EVENT
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete(); // Shreds the record row right out of the SQL database matching that ID key

        return redirect()->back()->with('success', 'Event has been permanently deleted.');
    }

    /**
 * 4. SHOW THE EDIT FORM WITH CURRENT DETAILS
 */
public function edit($id)
{
    // Find the single event by its ID, or fail with a 404 if it doesn't exist
    $event = Event::findOrFail($id);

    // Return an edit view file and pass the event data card along
    return view('hrview.hrdb_eventsedit', compact('event'));
}

/**
 * 5. SAVE THE UPDATED EVENT DETAILS
 */
public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);

    // Validate the incoming edits
    $validated = $request->validate([
        'eventName'               => 'required|string|max:255',
        'date'                    => 'required',
        'location'                => 'required|string|max:255',
        'wellcat'                 => 'required|in:physical,mental,financial,social',
        'description'             => 'required|string',
        'points_awarded_checkin'  => 'required|integer|min:0',
        'points_awarded_feedback' => 'required|integer|min:0',
    ]);

    // Perform the database update operation line items
    $event->update([
        'name'             => $validated['eventName'],
        'starts_at'        => $validated['date'],
        'location'         => $validated['location'],
        'category'         => $validated['wellcat'],
        'description'      => $validated['description'],
        'points_checkin'   => $validated['points_awarded_checkin'],
        'points_feedback'  => $validated['points_awarded_feedback'],
    ]);

    // Send back to the main management screen with a success alert banner note!
    return redirect()->route('events.management')->with('success', 'Event updated successfully!');
}
}
