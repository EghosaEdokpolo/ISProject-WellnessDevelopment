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
            'event_id'  => ['required', 'exists:events,id'],
            'recipient' => ['required', 'in:all,female,male,cafeteria,housekeeping,sces,sth'],
            'subject'   => ['nullable', 'string', 'max:255'],
            'body'      => ['nullable', 'string', 'max:5000'],
        ]);

        $event = Event::findOrFail($data['event_id']);

        $query = User::where('role', 'staff');

        switch ($data['recipient']) {
            case 'cafeteria':
                $query->where('department', 'like', '%Cafeteria%');
                break;
            case 'housekeeping':
                $query->where('department', 'like', '%Housekeep%');
                break;
            case 'sces':
                $query->where('department', 'like', '%Computing%');
                break;
            case 'sth':
                $query->where('department', 'like', '%Theology%');
                break;
            case 'female':
            case 'male':
            case 'all':
            default:
                break;
        }

        $staff = $query->get();

        if ($staff->isEmpty()) {
            return response()->json([
                'message'    => 'No staff match that category.',
                'sent_count' => 0,
            ]);
        }

        $subject = $data['subject'] ?? "Wellness Event: {$event->name}";
        $body    = $data['body']
                 ?? "You are invited to {$event->name} on "
                    . $event->starts_at->format('l, d F Y \a\t H:i')
                    . " at {$event->location}.\n\n"
                    . "Please log in to WPMIS to register.";

        $sent = 0;
        foreach ($staff as $person) {
            $email = "{$person->staff_id}@strathmore.edu";
            try {
                Mail::raw($body, function ($message) use ($email, $person, $subject) {
                    $message->to($email, $person->name)->subject($subject);
                });
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("Communication failed for {$person->name}: " . $e->getMessage());
            }
        }

        DataProcessingLog::record('communication_sent', [
            'event_id'           => $event->id,
            'event_name'         => $event->name,
            'recipient_category' => $data['recipient'],
            'recipient_count'    => $sent,
        ]);

        return response()->json([
            'message'    => "Sent to {$sent} staff member(s).",
            'sent_count' => $sent,
            'event'      => $event->name,
        ]);
    }
}