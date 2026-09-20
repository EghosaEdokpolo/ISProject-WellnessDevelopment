<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\PointService;

class IndexController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'points' => [
                'current_academic_year' => PointService::academicYear(),
                'next_reset'            => now()->startOfYear()->addYear()->toDateString(),
            ],
            'data_protection' => [
                'controller'       => 'Strathmore University, Madaraka Estate, Nairobi',
                'applicable_law'   => 'Kenya Data Protection Act, 2019',
                'retention_period' => '1 academic year from event date',
                'contact'          => 'dataprotection@strathmore.edu',
                'dpia_status'      => Setting::get('dpia_status', 'Initial DPIA completed'),
            ],
            'settings' => Setting::all(),
        ]);
    }
}