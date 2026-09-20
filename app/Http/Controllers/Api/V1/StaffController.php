<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        return StaffResource::collection(
            Staff::with('department')
                ->when($request->department_id, fn ($q, $id) => $q->where('department_id', $id))
                ->orderBy('last_name')
                ->paginate(25)
        );
    }

    public function show(Staff $staff): StaffResource
    {
        return new StaffResource($staff->load('department'));
    }
}