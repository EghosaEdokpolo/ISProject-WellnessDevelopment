<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;

class HrAttendeeController extends Controller
{
    /**
     * 1. INITIAL PAGE LOAD 
     * Fetches real event options directly to fill dropdown immediately.
     */
    public function index()
    {
        // Fetch all system events ordered by date sequence
        $events = Event::orderBy('starts_at', 'desc')->get();

        return view('hrview.hrdb_attendeetable', compact('events'));
    }

            /**
     * 2. ASYNC API DATA CHANNEL
     * Fetches individual row sets based on requested Event identification keys.
     */
    public function getAttendeesByEvent($eventId)
    {
        // Query registrations belonging to this specific event id along with user properties
        $registrations = Registration::with('user')
            ->where('event_id', $eventId)
            ->get();

        $formattedData = $registrations->map(function ($reg) {
            // Pull survey score records safely to fill out the numbers cells
            $preRecord  = $reg->wellbeingResponses()->where('stage', 'pre')->first();
            $postRecord = $reg->wellbeingResponses()->where('stage', 'post')->first();

            // 1. FORCE THE STATUS TO UPPERCASE FOR A BULLETPROOF MATCH ENGINE
            $dbStatus = strtoupper(str_replace([' ', '-'], '_', $reg->status ?? ''));

            // 2. MAP CHECKBOXES DIRECTLY BASED ON THE ACTUAL DATABASE STATUS ROW VALUES
            // - Registered is true for every record sitting in this table
            $hasRegistered = true; 
            
            // - Attended & Consent columns match 'CHECKED_IN' or 'COMPLETED'
            $hasAttended   = ($dbStatus === 'CHECKED_IN' || $dbStatus === 'COMPLETED');
            $hasConsented  = ($dbStatus === 'CHECKED_IN' || $dbStatus === 'COMPLETED');
            
            // - Feedback column is marked true ONLY if explicitly marked as 'COMPLETED'
            $hasFeedback   = ($dbStatus === 'COMPLETED');

            return [
                'id'                => $reg->id,
                'name'              => $reg->user->name ?? "Staff Member {$reg->user_id}",
                'department'        => $reg->user->department ?? 'General Staff',
                
                // Mirror mapping outputs to your view checkbox engines
                'registered'        => $hasRegistered, 
                'attended'          => $hasAttended,
                'consent'           => $hasConsented, 
                'feedback_provided' => $hasFeedback,
                
                'who5_pre'          => $preRecord ? $preRecord->total_score : null,
                'who5_post'         => $postRecord ? $postRecord->total_score : null,
                'points'            => $reg->user->points_balance ?? 0,
                
                // Passes the exact lowercase name string to fire your custom capsule CSS styles!
                'status'            => strtolower($dbStatus) 
            ];
        });    

        return response()->json($formattedData);
    }


}