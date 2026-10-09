<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// new controller homes
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Hr\HrAttendeeController;
use App\Http\Controllers\Hr\HrEventsController;
use App\Http\Controllers\Hr\HrOverviewController;
use App\Http\Controllers\Hr\HrImpactController;
use App\Http\Controllers\Hr\HrRetentionController;
use App\Http\Controllers\Hr\HrDepartmentController;
use App\Http\Controllers\Hr\HrSettingsController;

// Redirect root URL directly to the landing page
Route::get('/', function () {
    return view('welcome');
});


/**
 * The ['auth'] middleware checks if a browser cookie exists. 
 * If a hacker tries to visit any page inside here without logging in, 
 * Laravel kicks them straight back to the login screen automatically!
 */

// POINTER: 'auth' middleware means "must be logged in" — Laravel's built-in
// authentication (from `laravel/breeze` or `laravel/jetstream`, which you'd
// install for the login/register screens themselves) handles that part.
Route::middleware('auth')->group(function () {

    /**
     * 1. prefix('hr') means all URLs inside here will automatically start with /hr (e.g. /hr/overview)
     * 2. name('hr.') prefixes all shortcuts (e.g. route('hr.overview')) to keep naming conflicts clean
     */

    Route::prefix('hr')->group(function () {
        
        // Maps directly to the brand-new single-purpose controller index method!
        Route::get('/overview', [HrOverviewController::class, 'index'])->name('dashboard.hr');

        // This maps /hr/impact to our single-purpose controller while preserving your link name!
        Route::get('/impact', [HrImpactController::class, 'index'])->name('impact.analytics');
        
        // This maps /hr/retention to our new single-purpose controller while preserving your link name!
        Route::get('/retention', [HrRetentionController::class, 'index'])->name('retention.metrics');
        
        // This maps /hr/departmental to our new single-purpose controller while preserving your link name!
        Route::get('/departmental', [HrDepartmentController::class, 'index'])->name('department.analysis');
        
        // This maps /hr/attendees to our new single-purpose controller while preserving your link name!
        Route::get('/attendees', [HrAttendeeController::class, 'index'])->name('attendees.index');

        // This maps /hr/events to our new single-purpose controller while preserving your link name!
        Route::get('/events', [HrEventsController::class, 'index'])->name('events.management');

        // This maps /hr/settings to our new single-purpose controller while preserving your link name!
        Route::get('/settings', [HrSettingsController::class, 'index'])->name('settings.edit');
        
    });


    
    /** THE  STAFF MEMBER ZONE
     * Keeps employee personal views separated cleanly from administration data channels.
     */
    Route::prefix('staff')->name('staff.')->group(function () {
        // Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    });


    

    Route::get('/dashboard', function () {
        return auth()->user()->isHrCoordinator()
            ? redirect()->route('dashboard.hr')
            : redirect()->route('events.index');
    })->name('dashboard');

    // --- Shared / staff-facing routes ---
    Route::get('/events', [EventController::class, 'index'])->name('events.index');

    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])
        ->name('registrations.store');

    Route::get('/registrations/{registration}/waiver', [RegistrationController::class, 'waiverForm'])
        ->name('registrations.waiver');
    Route::post('/registrations/{registration}/waiver', [RegistrationController::class, 'waiverStore'])
        ->name('registrations.waiver.store');

    Route::get('/registrations/{registration}/checkin', [CheckInController::class, 'show'])
        ->name('checkin.show');

    // POINTER: the 'signed' middleware here is what actually ENFORCES the QR
    // code's signature + 15-minute expiry. If someone edits the URL or it's
    // past its expiry, Laravel auto-rejects it with a 403 before your
    // controller code even runs.
    Route::get('/checkin/confirm/{registration}', [CheckInController::class, 'confirm'])
        ->name('checkin.confirm')
        ->middleware('signed');

    Route::post('/checkin/manual', [CheckInController::class, 'manual'])->name('checkin.manual');

    Route::get('/registrations/{registration}/feedback', [FeedbackController::class, 'create'])
        ->name('feedback.create');
    Route::post('/registrations/{registration}/feedback', [FeedbackController::class, 'store'])
        ->name('feedback.store');

    Route::get('/rewards', [DashboardController::class, 'rewards'])->name('rewards.index');

});require __DIR__.'/auth.php';