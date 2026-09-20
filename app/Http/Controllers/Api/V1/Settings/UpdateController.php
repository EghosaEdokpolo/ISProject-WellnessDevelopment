<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Controller;
use App\Models\DataProcessingLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
        ]);

        foreach ($data['settings'] as $key => $value) {
            Setting::put($key, $value);
        }

        DataProcessingLog::record('settings_updated', ['keys' => array_keys($data['settings'])]);

        return response()->json(['message' => 'Settings updated.']);
    }
}