<?php

declare(strict_types=1);

return [
    'timezone' => env('WELLLOOP_TIMEZONE', 'Africa/Nairobi'),

    'checkin' => [
        'open_before_minutes'  => 60, // 1 hr before start up to start time: 100% full points
        'late_grace_minutes'   => 15, // 1–15 mins after start: 50% half points; 16+ mins: closed / 0 points
        'late_penalty_percent' => 50,
    ],

    'cache' => [
        'hr_overview_key'  => 'hr_overview_data_v21',
        'hr_impact_prefix' => 'hr_impact_data_v6_',
    ],
];