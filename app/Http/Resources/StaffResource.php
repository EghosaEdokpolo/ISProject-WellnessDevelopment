<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'staff_id_number' => $this->staff_id_number,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'full_name'       => $this->fullName(),
            'email'           => $this->email,
            'phone'           => $this->phone,
            'role'            => $this->role->value,
            'points_balance'  => $this->points_balance,
            'consent_given'   => $this->consent_given,
            'is_active'       => $this->is_active,
            'department'      => new DepartmentResource($this->whenLoaded('department')),
        ];
    }
}