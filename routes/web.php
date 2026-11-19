<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- PUBLIC / SIGNED VENUE QR CHECK-IN ROUTES (Outside auth middleware) ---
Route::get('/checkin/event/{event}', [CheckInController::class, 'scan'])->name('checkin.scan');
Route::post('/checkin/event/{event}/verify-staff', [CheckInController::class, 'verifyStaffScan'])->name('checkin.scan.verify');

// --- AUTHENTICATION ROUTES (Guest Access) ---
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// --- AUTHENTICATED ROUTES ---
Route::middleware('auth')->group(function (): void {

    Route::get('/dashboard', function () {
        return auth()->user()->isHrCoordinator()
            ? redirect()->route('dashboard.hr')
            : redirect()->route('events.index');
    })->name('dashboard');

    // --- STAFF ROUTES ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}/register', [RegistrationController::class, 'create'])->name('events.register');
    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');

    // Pre-Event WHO-5 Baseline & Mandatory Digital Waiver Routes
    Route::get('/registrations/{registration}/who5', [RegistrationController::class, 'who5Form'])->name('registrations.who5');
    Route::post('/registrations/{registration}/who5', [RegistrationController::class, 'who5Store'])->name('registrations.who5.store');
    Route::get('/registrations/{registration}/waiver', [RegistrationController::class, 'waiverForm'])->name('registrations.waiver');
    Route::post('/registrations/{registration}/waiver', [RegistrationController::class, 'waiverStore'])->name('registrations.waiver.store');
    Route::get('/registrations/{registration}/consent', [RegistrationController::class, 'consentForm'])->name('registrations.consent');
    Route::post('/registrations/{registration}/consent', [RegistrationController::class, 'consentStore'])->name('registrations.consent.store');

    // Official Event Pass & Venue Check-In Routes
    Route::get('/registrations/{registration}/ticket', [CheckInController::class, 'ticket'])->name('registrations.ticket');
    Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.index');
    Route::get('/checkin/{registration}', [CheckInController::class, 'show'])->name('checkin.show');
    Route::post('/checkin/{registration}/confirm', [CheckInController::class, 'confirm'])->name('checkin.confirm');
    Route::post('/checkin/manual', [CheckInController::class, 'manual'])->name('checkin.manual');

    // Post-Event WHO-5 & Feedback Routes
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/registrations/{registration}/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/registrations/{registration}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    // Rewards & Redemption Routes
    Route::get('/rewards', [DashboardController::class, 'rewards'])->name('rewards.index');
    Route::post('/rewards/redeem', [DashboardController::class, 'redeemReward'])->name('rewards.redeem');

    // Wellness Content Hub Routes
    Route::get('/content', [ContentController::class, 'index'])->name('content.index');
    Route::get('/content/{slug}', [ContentController::class, 'show'])->name('content.show');
    Route::post('/content/{slug}/bookmark', [ContentController::class, 'toggleSave'])->name('content.bookmark');
    Route::post('/content/{slug}/save', [ContentController::class, 'toggleSave'])->name('content.save');

    // --- HR COORDINATOR ROUTES ---
    Route::middleware('hr')->group(function (): void {

        Route::get('/hr/overview', [DashboardController::class, 'hrOverview'])->name('dashboard.hr');
        Route::get('/impact-analytics', [DashboardController::class, 'impactAnalytics'])->name('impact.analytics');
        Route::get('/retention-metrics', [DashboardController::class, 'retentionMetrics'])->name('retention.metrics');
        Route::get('/department-analysis', [DashboardController::class, 'departmentAnalysis'])->name('department.analysis');
        Route::get('/attendees', [DashboardController::class, 'attendees'])->name('attendees.index');
        Route::get('/attendees/export', [DashboardController::class, 'exportAttendeesCsv'])->name('attendees.export');
        Route::post('/attendees/historical', [DashboardController::class, 'logHistoricalAttendance'])->name('attendees.historical');

        Route::get('/hr/feedback-logs', function () {
            return redirect()->route('attendees.index');
        })->name('hr.feedback.logs');

        // Event Management & QR Code Generation
        Route::get('/events-management', [EventController::class, 'management'])->name('events.management');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        Route::get('/events/{event}/qr', QrCodeController::class)->name('events.qr');
        Route::get('/events/{event}/qr-poster', [CheckInController::class, 'poster'])->name('events.qr.poster');

        // Settings
        Route::get('/settings', [DashboardController::class, 'settings'])->name('settings.edit');
        Route::post('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/reset-points', [DashboardController::class, 'resetPoints'])->name('settings.reset_points');

        // Reports & Communications
        Route::get('/reports/departmental-export', DepartmentalExportController::class)->name('reports.departmental.export');
        Route::get('/reports/pdf-export', PdfExportController::class)->name('reports.pdf.export');
        Route::post('/communications/send', [CommunicationController::class, 'send'])->name('communications.send');
    });
});