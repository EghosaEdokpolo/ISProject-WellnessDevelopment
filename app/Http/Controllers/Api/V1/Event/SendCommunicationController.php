<?php

namespace App\Http\Controllers\Api\V1\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SendCommunicationRequest;
use App\Models\Communication;
use App\Models\Event;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;

class SendCommunicationController extends Controller
{
    public function __invoke(SendCommunicationRequest $request, Event $event): JsonResponse
    {
        $filter = $request->input('recipient_filter', []);
        $recipients = Staff::where('is_active', true)
            ->when(!empty($filter['department_id']), fn ($q) => $q->where('department_id', $filter['department_id']))
            ->get();

        $comm = Communication::create([
            'event_id'         => $event->id,
            'channel'          => $request->channel,
            'subject'          => $request->subject ?? "Wellness Event: {$event->title}",
            'body'             => $request->body ?? "You are invited to {$event->title} on {$event->starts_at->toDayDateTimeString()} at {$event->venue}.",
            'recipient_filter' => $filter,
            'recipient_count'  => $recipients->count(),
            'sent_by'          => $request->user()->id,
            'status'           => 'queued',
        ]);

        // NOTE: Actual email/calendar dispatch is stubbed for now.
        // Wire up a Mail::to(...) loop or Google Calendar API here later.
        $comm->update(['status' => 'sent', 'sent_at' => now()]);

        return response()->json([
            'message'         => 'Communication queued.',
            'communication_id'=> $comm->id,
            'recipient_count' => $recipients->count(),
        ]);
    }
}