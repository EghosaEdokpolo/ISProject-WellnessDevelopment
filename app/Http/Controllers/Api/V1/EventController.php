<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreEventRequest;
use App\Http\Requests\Api\V1\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        return EventResource::collection(
            Event::with('creator')
                ->when($request->category, fn ($q, $c) => $q->where('wellness_category', $c))
                ->when($request->published !== null, fn ($q) => $q->where('is_published', filter_var($request->published, FILTER_VALIDATE_BOOLEAN)))
                ->orderByDesc('starts_at')
                ->paginate(15)
        );
    }

    public function store(StoreEventRequest $request)
    {
        $event = Event::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return (new EventResource($event->load('creator')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Event $event): EventResource
    {
        return new EventResource($event->load(['creator']));
    }

    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        $event->update($request->validated());
        return new EventResource($event->fresh('creator'));
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(status: 204);
    }
}