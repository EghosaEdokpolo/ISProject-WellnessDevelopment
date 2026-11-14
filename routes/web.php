<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Reports\DepartmentalExportController;
use App\Http\Controllers\Reports\PdfExportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- AUTHENTICATION ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');


// --- AUTHENTICATED ROUTES ---
Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', function () {
        return auth()->user()->isHrCoordinator() 
            ? redirect()->route('dashboard.hr') 
            : redirect()->route('events.index');
    })->name('dashboard');

    // --- STAFF ROUTES ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}/register', [RegistrationController::class, 'create'])->name('registrations.create');
    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::get('/registrations/{registration}/baseline', [RegistrationController::class, 'who5Form'])->name('registrations.who5');
    Route::post('/registrations/{registration}/baseline', [RegistrationController::class, 'who5Store'])->name('registrations.who5.store');
    Route::get('/registrations/{registration}/waiver', [RegistrationController::class, 'waiverForm'])->name('registrations.waiver');
    Route::post('/registrations/{registration}/waiver', [RegistrationController::class, 'waiverStore'])->name('registrations.waiver.store');
    Route::get('/registrations/{registration}/consent', [RegistrationController::class, 'consentForm'])->name('registrations.consent');
    Route::post('/registrations/{registration}/consent', [RegistrationController::class, 'consentStore'])->name('registrations.consent.store');
    Route::get('/registrations/{registration}/ticket', [CheckInController::class, 'ticket'])->name('registrations.ticket');

    Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.index');
    Route::get('/checkin/event/{event}', [CheckInController::class, 'scan'])->name('checkin.scan');
    Route::get('/checkin/{registration}', [CheckInController::class, 'show'])->name('checkin.show');
    Route::post('/checkin/{registration}/confirm', [CheckInController::class, 'confirm'])->name('checkin.confirm');
    Route::post('/checkin/manual', [CheckInController::class, 'manual'])->name('checkin.manual');

    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/registrations/{registration}/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/registrations/{registration}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    Route::get('/rewards', [DashboardController::class, 'rewards'])->name('rewards.index');

    Route::get('/content', [ContentController::class, 'index'])->name('content.index');
    Route::get('/content/{slug}', [ContentController::class, 'show'])->name('content.show');
    Route::post('/content/{slug}/save', [ContentController::class, 'toggleSave'])->name('content.save');


    // --- HR COORDINATOR ROUTES ---
    Route::middleware('hr')->group(function () {
        
        Route::get('/hr/overview', [DashboardController::class, 'hrOverview'])->name('dashboard.hr');
        Route::get('/impact-analytics', [DashboardController::class, 'impactAnalytics'])->name('impact.analytics');
        Route::get('/retention-metrics', [DashboardController::class, 'retentionMetrics'])->name('retention.metrics');
        Route::get('/department-analysis', [DashboardController::class, 'departmentAnalysis'])->name('department.analysis');
        Route::get('/attendees', [DashboardController::class, 'attendees'])->name('attendees.index');
        
        Route::get('/events-management', [DashboardController::class, 'eventmanagement'])->name('events.management');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::get('/events/{event}/qr', QrCodeController::class)->name('events.qr');
        
        // User Management (WITH EDIT/UPDATE ROUTES)
        Route::get('/manage-users', [UserManagementController::class, 'index'])->name('hr.users.index');
        Route::post('/manage-users', [UserManagementController::class, 'store'])->name('hr.users.store');
        Route::post('/manage-users/import', [UserManagementController::class, 'import'])->name('hr.users.import');
        Route::get('/manage-users/{user}/edit', [UserManagementController::class, 'edit'])->name('hr.users.edit');
        Route::put('/manage-users/{user}', [UserManagementController::class, 'update'])->name('hr.users.update');

        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings.edit');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/reset-points', [DashboardController::class, 'resetPoints'])->name('settings.reset_points');

        Route::get('/reports/departmental-export', DepartmentalExportController::class)->name('reports.departmental.export');
        Route::get('/reports/pdf-export', PdfExportController::class)->name('reports.pdf.export');
        
        Route::post('/communications/send', [CommunicationController::class, 'send'])->name('communications.send');
    });
});