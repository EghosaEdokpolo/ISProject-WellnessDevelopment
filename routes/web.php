<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Reports\DepartmentalExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->isHrCoordinator()
            ? redirect()->route('dashboard.hr')
            : redirect()->route('events.index');
    })->name('dashboard');

    // Staff routes
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::get('/registrations/{registration}/waiver', [RegistrationController::class, 'waiverForm'])->name('registrations.waiver');
    Route::post('/registrations/{registration}/waiver', [RegistrationController::class, 'waiverStore'])->name('registrations.waiver.store');
    Route::get('/registrations/{registration}/checkin', [CheckInController::class, 'show'])->name('checkin.show');
    Route::get('/checkin/confirm/{registration}', [CheckInController::class, 'confirm'])->name('checkin.confirm')->middleware('signed');
    Route::post('/checkin/manual', [CheckInController::class, 'manual'])->name('checkin.manual');
    Route::get('/registrations/{registration}/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/registrations/{registration}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/rewards', [DashboardController::class, 'rewards'])->name('rewards.index');

    // HR routes
    Route::middleware('hr')->group(function () {
        Route::get('/hr/overview', [DashboardController::class, 'hrOverview'])->name('dashboard.hr');
        Route::get('/impact-analytics', [DashboardController::class, 'impactAnalytics'])->name('impact.analytics');
        Route::get('/retention-metrics', [DashboardController::class, 'retentionMetrics'])->name('retention.metrics');
        Route::get('/department-analysis', [DashboardController::class, 'departmentAnalysis'])->name('department.analysis');
        Route::get('/attendees', [DashboardController::class, 'attendees'])->name('attendees.index');
        Route::get('/events-management', [DashboardController::class, 'eventmanagement'])->name('events.management');
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings.edit');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');

        // Event actions
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        
        // Advanced features
        Route::get('/events/{event}/qr', QrCodeController::class)->name('events.qr');
        Route::get('/reports/departmental-export', DepartmentalExportController::class)->name('reports.departmental.export');
        Route::post('/communications/send', [CommunicationController::class, 'send'])->name('communications.send');
    });
});

require __DIR__.'/auth.php';