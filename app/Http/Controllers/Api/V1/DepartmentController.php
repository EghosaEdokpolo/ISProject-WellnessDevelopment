<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        return DepartmentResource::collection(Department::orderBy('name')->get());
    }

    public function show(Department $department): DepartmentResource
    {
        return new DepartmentResource($department);
    }
}