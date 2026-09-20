<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventQrToken;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckInController extends Controller
{
    public function __invoke(Request $request, Event $event, PointService $points): JsonResponse
    {
        $token = $request->query('token') ?? $request->input('token');

        if ($token) {
            $valid = EventQrToken::where('event_id', $event->id)
                ->where('token', $token)
                ->where('expires_at', '>', now())
                ->exists();

            abort_unless($valid, 422, 'Invalid or expired QR token.');
        }

        $staff = $request->user();

        $attendance = EventAttendance::updateOrCreate(
            ['event_id' => $event->id, 'staff_id' => $staff->id],
            [
                'attended'      => true,
                'qr_checked_in' => (bool) $token,
                'checked_in_at' => now(),
                'registered_at' => now(),
            ]
        );

        // Award points once
        $alreadyAwarded = $staff->pointsTransactions()
            ->where('event_id', $event->id)
            ->where('reason', 'event_attendance')
            ->exists();

        if (! $alreadyAwarded && $event->points_awarded > 0) {
            $points->award($staff, $event->points_awarded, 'event_attendance', $event->id);
        }

        return response()->json([
            'message'    => 'Checked in successfully.',
            'attendance' => $attendance->fresh(),
        ]);
    }
}