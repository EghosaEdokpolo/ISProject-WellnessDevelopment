<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'description'       => $this->description,
            'wellness_category' => $this->wellness_category->value,
            'category_label'    => $this->wellness_category->label(),
            'starts_at'         => $this->starts_at->toIso8601String(),
            'ends_at'           => $this->ends_at?->toIso8601String(),
            'venue'             => $this->venue,
            'capacity'          => $this->capacity,
            'points_awarded'    => $this->points_awarded,
            'is_published'      => $this->is_published,
            'published_at'      => $this->published_at?->toIso8601String(),
            'creator'           => new StaffResource($this->whenLoaded('creator')),
            'created_at'        => $this->created_at->toIso8601String(),
        ];
    }
}