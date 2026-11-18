<?php

declare(strict_types=1);

return [
    'timezone' => env('APP_TIMEZONE', 'Africa/Nairobi'),

    'checkin' => [
        // Gate opens 60 minutes (1 hour) before event starts_at
        'opens_minutes_before' => (int) env('WELLLOOP_CHECKIN_OPENS_BEFORE', 60),

        // Allowance of 10 minutes after starts_at before the gate closes
        'late_grace_minutes'   => (int) env('WELLLOOP_CHECKIN_LATE_GRACE_MINUTES', 10),

        // Percentage reduction applied to check-in points during the 10-minute late grace window
        'late_penalty_percent' => (int) env('WELLLOOP_CHECKIN_LATE_PENALTY_PERCENT', 40),

        // Fallback check-in points if null on event
        'default_points'       => (int) env('WELLLOOP_DEFAULT_CHECKIN_POINTS', 25),

        // Allow walk-in staff to auto-register when scanning the venue QR poster
        'allow_walk_in_qr'     => (bool) env('WELLLOOP_ALLOW_WALK_IN_QR', true),
    ],

    'cache' => [
        'hr_overview_key'  => 'hr_overview_data_v21',
        'hr_impact_prefix' => 'hr_impact_data_v6_',
    ],
];