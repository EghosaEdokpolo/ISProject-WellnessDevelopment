<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'staff_id_number' => ['required', 'string'],
            'password'        => ['required', 'string'],
            'device_name'     => ['nullable', 'string'],
        ]);

        $staff = Staff::where('staff_id_number', $data['staff_id_number'])->first();

        if (! $staff || ! Hash::check($data['password'], $staff->password)) {
            throw ValidationException::withMessages([
                'staff_id_number' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $staff->is_active) {
            throw ValidationException::withMessages([
                'staff_id_number' => ['This account is inactive.'],
            ]);
        }

        $token = $staff->createToken(
            $data['device_name'] ?? 'wpmis-token',
            expiresAt: now()->addDays(30),
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'staff' => new StaffResource($staff->load('department')),
        ]);
    }
}