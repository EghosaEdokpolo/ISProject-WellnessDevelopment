<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Reports\DepartmentalExportController;
use App\Http\Controllers\Reports\PdfExportController;
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
    
    Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.index');
    Route::get('/checkin/event/{event}', [CheckInController::class, 'scan'])->name('checkin.scan');
    Route::get('/checkin/{registration}', [CheckInController::class, 'show'])->name('checkin.show');
    Route::post('/checkin/{registration}/confirm', [CheckInController::class, 'confirm'])->name('checkin.confirm');
    
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/registrations/{registration}/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/registrations/{registration}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    
    Route::get('/rewards', [DashboardController::class, 'rewards'])->name('rewards.index');
    Route::get('/content', function () { return view('content.index'); })->name('content.index');

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

        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::get('/events/{event}/qr', QrCodeController::class)->name('events.qr');
        
        // Export Routes
        Route::get('/reports/departmental-export', DepartmentalExportController::class)->name('reports.departmental.export');
        Route::get('/reports/pdf-export', PdfExportController::class)->name('reports.pdf.export');
        
        Route::post('/communications/send', [CommunicationController::class, 'send'])->name('communications.send');
    });
});

require __DIR__.'/auth.php';