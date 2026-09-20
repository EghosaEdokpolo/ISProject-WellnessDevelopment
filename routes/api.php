<?php

use App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('auth/login', V1\Auth\LoginController::class);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', V1\Auth\LogoutController::class);
        Route::get('auth/me',      V1\Auth\MeController::class);

        Route::get('dashboard/overview',     V1\Dashboard\OverviewController::class);
        Route::get('dashboard/impact',       V1\Dashboard\ImpactController::class);
        Route::get('dashboard/retention',    V1\Dashboard\RetentionController::class);
        Route::get('dashboard/departmental', V1\Dashboard\DepartmentalController::class);

        Route::apiResource('staff', V1\StaffController::class)->only(['index', 'show']);
        Route::apiResource('departments', V1\DepartmentController::class)->only(['index', 'show']);

        Route::apiResource('events', V1\EventController::class);
        Route::post('events/{event}/publish',            V1\Event\PublishController::class);
        Route::post('events/{event}/send-communication', V1\Event\SendCommunicationController::class);
        Route::get('events/{event}/qr',                  V1\Event\QrCodeController::class);

        Route::post('events/{event}/check-in', V1\Attendance\CheckInController::class);
        Route::get('events/{event}/attendees', V1\Attendance\IndexController::class);

        Route::post('events/{event}/assessments',        V1\Assessment\StoreController::class);
        Route::get('events/{event}/assessments/summary', V1\Assessment\SummaryController::class);

        Route::post('events/{event}/feedback', V1\Feedback\StoreController::class);

        Route::get('staff/{staff}/points', V1\Points\HistoryController::class);
        Route::post('points/reset',        V1\Points\ResetController::class);

        Route::get('reports/impact-trends',       V1\Reports\ImpactTrendController::class);
        Route::get('reports/departmental-export', V1\Reports\DepartmentalExportController::class);

        Route::get('settings', V1\Settings\IndexController::class);
        Route::put('settings', V1\Settings\UpdateController::class);
    });
});