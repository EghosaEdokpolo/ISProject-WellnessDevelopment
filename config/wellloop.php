<?php

declare(strict_types=1);

return [
    'timezone' => env('APP_TIMEZONE', 'Africa/Nairobi'),

    'checkin' => [
        // Gate opens 60 minutes (1 hour) before event starts_at
        'opens_minutes_before' => (int) env('WELLLOOP_CHECKIN_OPENS_BEFORE', 60),

        // Gate closes 8 hours after event starts_at
        'closes_hours_after'   => (int) env('WELLLOOP_CHECKIN_CLOSES_AFTER', 8),

        // Default check-in points if null on event
        'default_points'       => (int) env('WELLLOOP_DEFAULT_CHECKIN_POINTS', 20),

        // Allow walk-in staff to auto-register when scanning the venue QR poster
        'allow_walk_in_qr'     => (bool) env('WELLLOOP_ALLOW_WALK_IN_QR', true),
    ],

    'cache' => [
        'hr_overview_key'  => 'hr_overview_data_v21',
        'hr_impact_prefix' => 'hr_impact_data_v6_',
    ],
];