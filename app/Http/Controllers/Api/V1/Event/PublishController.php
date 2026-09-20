<?php

namespace App\Http\Controllers\Api\V1\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class PublishController extends Controller
{
    public function __invoke(Request $request, Event $event): EventResource
    {
        abort_unless($request->user()->role->canManageEvents(), 403);

        $event->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return new EventResource($event->fresh('creator'));
    }
}