<?php

use Illuminate\Support\Facades\Route;

// Login
Route::get('/', fn () => view('auth.login'))->name('login');
Route::get('/loginpage.html', fn () => view('auth.login'));

// Dashboard pages — no auth middleware yet (we add it after login works)
Route::get('/hrdb_overview.html',        fn () => view('dashboard.overview'))->name('overview');
Route::get('/hrdb_impact.html',          fn () => view('dashboard.impact'))->name('impact');
Route::get('/hrdb_retention.html',       fn () => view('dashboard.retention'))->name('retention');
Route::get('/hrdb_departmental.html',    fn () => view('dashboard.departmental'))->name('departmental');
Route::get('/hrdb_attendeetable.html',   fn () => view('dashboard.attendees'))->name('attendees');
Route::get('/hrdb_eventsmanagement.html', fn () => view('dashboard.events'))->name('events');
Route::get('/hrdb_settings.html',        fn () => view('dashboard.settings'))->name('settings');

// Clean URLs
Route::get('/overview',     fn () => view('dashboard.overview'));
Route::get('/impact',       fn () => view('dashboard.impact'));
Route::get('/retention',    fn () => view('dashboard.retention'));
Route::get('/departmental', fn () => view('dashboard.departmental'));
Route::get('/attendees',    fn () => view('dashboard.attendees'));
Route::get('/events',       fn () => view('dashboard.events'));
Route::get('/settings',     fn () => view('dashboard.settings'));