<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// POINTER: 'auth' middleware means "must be logged in" — Laravel's built-in
// authentication (from `laravel/breeze` or `laravel/jetstream`, which you'd
// install for the login/register screens themselves) handles that part.
Route::middleware('auth')->group(function () {

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

    // --- HR Coordinator-only routes ---
    Route::middleware('hr')->group(function () {
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::get('/dashboard/overview', [DashboardController::class, 'hrOverview'])->name('dashboard.hr');
        Route::get('/attendees', [DashboardController::class, 'attendees'])->name('attendees.index');
    });
});require __DIR__.'/auth.php';