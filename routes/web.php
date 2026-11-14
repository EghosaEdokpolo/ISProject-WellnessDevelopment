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
use App\Http\Controllers\HrImpactController;
use App\Http\Controllers\HrSettingsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


Route::get('/', function () {
    return view('welcome');
});

    // Public Venue Arrival QR Check-In Routes (No login required on phone)
    Route::get('/checkin/scan/{event}', [CheckInController::class, 'arrivalForm'])->name('checkin.scan');
    Route::post('/checkin/scan/{event}', [CheckInController::class, 'verifyArrival'])->name('checkin.verify');


Route::middleware('auth')->group(function () {

    // Let AuthenticatedSessionController destroy the login session
    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

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
    
    // Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.index');
    // Route::get('/checkin/event/{event}', [CheckInController::class, 'scan'])->name('checkin.scan');
    // Route::get('/checkin/{registration}', [CheckInController::class, 'show'])->name('checkin.show');
    // Route::post('/checkin/{registration}/confirm', [CheckInController::class, 'confirm'])->name('checkin.confirm');
    
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/registrations/{registration}/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/registrations/{registration}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    
    Route::get('/rewards', [DashboardController::class, 'rewards'])->name('rewards.index');
    Route::get('/content', function () { return view('content.index'); })->name('content.index');

    // HR routes
    Route::middleware('hr')->group(function () {
        Route::get('/hr/overview', [DashboardController::class, 'hrOverview'])->name('dashboard.hr');
        Route::get('/impact-analytics', [HrImpactController::class, 'index'])->name('impact.analytics');
        Route::get('/retention-metrics', [DashboardController::class, 'retentionMetrics'])->name('retention.metrics');
        Route::get('/department-analysis', [DashboardController::class, 'departmentAnalysis'])->name('department.analysis');
        Route::get('/attendees', [DashboardController::class, 'attendees'])->name('attendees.index');
        Route::get('/events-management', [DashboardController::class, 'eventmanagement'])->name('events.management');
        
        //settings routes
        //Route::get('/settings', [DashboardController::class, 'settings'])->name('settings.edit');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');

        // This maps /hr/settings to our new single-purpose controller while preserving your link name!
        Route::get('/settings', [HrSettingsController::class, 'index'])->name('settings.edit');
            
        // Route to handle the points reset button action
            Route::post('/points/reset', [HrSettingsController::class, 'resetPoints']);

        
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::get('/events/{event}/qr', QrCodeController::class)->name('events.qr');
        
        // Export Routes
        Route::get('/reports/departmental-export', DepartmentalExportController::class)->name('reports.departmental.export');
        Route::get('/reports/pdf-export', PdfExportController::class)->name('reports.pdf.export');
        
        Route::post('/communications/send', [CommunicationController::class, 'send'])->name('communications.send');
    
        //feedback routes
        Route::get('/hr/feedback-logs', [DashboardController::class, 'feedbackLogs'])->name('hr.feedback.logs');
        
        // Event Edit, Update, and Delete Routes
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    });
    
});

require __DIR__.'/auth.php';