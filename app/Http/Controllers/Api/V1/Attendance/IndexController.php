<?php

namespace App\Http\Controllers\Api\V1\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Models\Event;
use App\Models\EventAttendance;

class IndexController extends Controller
{
    public function __invoke(Event $event)
    {
        $rows = EventAttendance::with([
                'staff.department',
                'staff.assessments',
                'staff.feedback',
            ])
            ->where('event_id', $event->id)
            ->get();

        return AttendanceResource::collection($rows);
    }
}