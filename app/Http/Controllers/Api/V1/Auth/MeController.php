<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffResource;

class MeController extends Controller
{
    public function __invoke(): StaffResource
    {
        return new StaffResource(auth()->user()->load('department'));
    }
}