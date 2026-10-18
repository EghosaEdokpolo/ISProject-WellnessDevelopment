<?php

namespace App\Http\Controllers;

use App\Models\DataProcessingLog;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CommunicationController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'recipient' => ['required', 'in:all,female,male,cafeteria,housekeeping,sces,sth'],
        ]);

        $event = Event::findOrFail($data['event_id']);
        $query = User::where('role', 'staff');

        if ($data['recipient'] === 'cafeteria') $query->where('department', 'like', '%Cafeteria%');
        if ($data['recipient'] === 'housekeeping') $query->where('department', 'like', '%Housekeep%');
        if ($data['recipient'] === 'sces') $query->where('department', 'like', '%Computing%');
        if ($data['recipient'] === 'sth') $query->where('department', 'like', '%Theology%');

        $staff = $query->get();
        if ($staff->isEmpty()) return response()->json(['message' => 'No staff match.', 'sent_count' => 0]);

        $subject = "Wellness Event: {$event->name}";
        $body = "You are invited to {$event->name} on {$event->starts_at->format('l, d F Y \a\t H:i')} at {$event->location}.\n\nPlease log in to WPMIS to register.";

        $sent = 0;
        foreach ($staff as $person) {
            try {
                Mail::raw($body, function ($message) use ($person, $subject) {
                    $message->to("{$person->staff_id}@strathmore.edu", $person->name)->subject($subject);
                });
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("Communication failed for {$person->name}: " . $e->getMessage());
            }
        }

        DataProcessingLog::record('communication_sent', ['event_id' => $event->id, 'count' => $sent]);

        return response()->json(['message' => "Sent to {$sent} staff.", 'sent_count' => $sent]);
    }
}