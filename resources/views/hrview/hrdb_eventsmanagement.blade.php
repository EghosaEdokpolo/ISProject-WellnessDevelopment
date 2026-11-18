@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar {
        font-family: 'Montserrat', sans-serif !important;
        border-radius: 16px !important;
        box-shadow: 0 12px 32px rgba(2, 51, 141, 0.16) !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden;
    }
    .flatpickr-day.selected, .flatpickr-day.selected:hover {
        background: #02338d !important;
        border-color: #02338d !important;
    }
    .flatpickr-time {
        border-top: 1px solid #e2e8f0 !important;
        padding: 4px 8px !important;
    }
    .flatpickr-time .flatpickr-am-pm {
        background: #e8f0fe !important;
        color: #02338d !important;
        font-weight: 800 !important;
        border-radius: 8px !important;
        margin: 4px !important;
        cursor: pointer !important;
        transition: all 0.15s ease;
    }
    .flatpickr-time .flatpickr-am-pm:hover {
        background: #02338d !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
@php
    $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 10);
    $now              = now($tz);
    $cutoffTime       = $cutoff ?? $now->copy()->subMinutes($lateGraceMinutes);

    $allEvents = isset($events)
        ? $events
        : \App\Models\Event::withCount(['registrations', 'checkIns'])->orderByDesc('starts_at')->get();

    $upcomingList  = $allEvents->filter(fn ($e) => $e->starts_at && $e->starts_at->copy()->shiftTimezone($tz)->gt($cutoffTime))->values();
    $upcomingCount = $upcomingList->count();
    $pastCount     = $allEvents->count() - $upcomingCount;

    // Issue 1 Fix: Empty by default! ONLY pre-select if an active/upcoming session was explicitly requested or just created
    $requestedQrId  = (int) (session('qr_event_id') ?? request('qr_event', 0));
    $initialQrEvent = $requestedQrId > 0 ? $upcomingList->firstWhere('id', $requestedQrId) : null;

    $pillars = [
        'physical'      => 'Physical Wellness',
        'mental'        => 'Mental & Emotional',
        'social'        => 'Social & Community',
        'financial'     => 'Financial Wellbeing',
        'intellectual'  => 'Intellectual Growth',
        'occupational'  => 'Occupational Balance',
        'environmental' => 'Environmental Stewardship',
        'spiritual'     => 'Spiritual & Purpose',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
                People & Culture · Event Operations
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                Wellness Event Management
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Schedule campus wellness sessions, attach WhatsApp channels, and download venue check-in QR codes.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="bg-white border border-slate-200 rounded-2xl px-4 py-2 text-center shadow-sm">
                <span class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Upcoming</span>
                <span class="text-base font-extrabold text-emerald-600">{{ $upcomingCount }}</span>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl px-4 py-2 text-center shadow-sm">
                <span class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Past Events</span>
                <span class="text-base font-extrabold text-slate-600">{{ $pastCount }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-extrabold border border-emerald-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-xs font-bold border border-rose-200 shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main 12-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left Column: Schedule New Session Form (5 Cols) -->
        <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-[#02338d] to-[#c99a2e]"></div>
            <div class="p-6 sm:p-7">
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="w-1.5 h-5 bg-[#02338d] rounded-full"></span>
                    <h2 class="text-lg font-extrabold text-slate-900">Schedule New Session</h2>
                </div>
                <p class="text-xs text-slate-500 mb-6">
                    Gate opens automatically 1 hour before start and closes 10 minutes after start (EAT).
                </p>

                <form method="POST" action="{{ route('events.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                            Event Title <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               placeholder="e.g., Stress Management & Mindfulness Lab"
                               class="w-full border border-slate-200 rounded-xl text-xs font-bold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                                Wellness Pillar <span class="text-rose-500">*</span>
                            </label>
                            <select name="category"
                                    required
                                    class="w-full border border-slate-200 rounded-xl text-xs font-bold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                                @foreach($pillars as $key => $label)
                                    <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                                Date & Start Time (EAT) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative flex items-center" id="create-datetime-picker">
                                <input type="text"
                                       name="starts_at"
                                       id="starts_at_input"
                                       value="{{ old('starts_at') }}"
                                       placeholder="Click calendar icon..."
                                       data-input
                                       required
                                       readonly
                                       class="w-full border border-slate-200 rounded-xl text-xs font-bold py-3 pl-3 pr-10 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d] cursor-pointer">
                                <button type="button"
                                        title="Open Calendar & AM/PM Time Picker"
                                        data-toggle
                                        class="absolute right-2.5 text-[#02338d] hover:text-[#01225e] p-1 rounded-lg hover:bg-blue-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p id="datetime-clamp-notice" class="hidden text-[10px] font-extrabold text-amber-700 mt-1">
                                Past time prevented — automatically set to current EAT time.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                                Campus Venue <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="location"
                                   value="{{ old('location') }}"
                                   required
                                   placeholder="e.g., Main Campus - Auditorium"
                                   class="w-full border border-slate-200 rounded-xl text-xs font-bold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                        </div>

                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                                Capacity <span class="text-rose-500">*</span>
                            </label>
                            <input type="number"
                                   name="capacity"
                                   value="{{ old('capacity', 80) }}"
                                   min="1"
                                   required
                                   class="w-full border border-slate-200 rounded-xl text-xs font-bold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                                Check-In Points <span class="text-rose-500">*</span>
                            </label>
                            <input type="number"
                                   name="points_checkin"
                                   value="{{ old('points_checkin', 25) }}"
                                   min="0"
                                   required
                                   class="w-full border border-slate-200 rounded-xl text-xs font-bold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                        </div>

                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                                Post-WHO Points <span class="text-rose-500">*</span>
                            </label>
                            <input type="number"
                                   name="points_feedback"
                                   value="{{ old('points_feedback', 10) }}"
                                   min="0"
                                   required
                                   class="w-full border border-slate-200 rounded-xl text-xs font-bold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-0.5">
                            Event WhatsApp Channel / Group URL
                        </label>
                        <p class="text-[11px] text-slate-400 italic mb-1.5">
                            Staff receive a direct "Join WhatsApp Channel" button after registering and on their Official Event Pass.
                        </p>
                        <input type="text"
                               name="whatsapp_channel_url"
                               value="{{ old('whatsapp_channel_url') }}"
                               placeholder="https://chat.whatsapp.com/... or https://whatsapp.com/channel/..."
                               class="w-full border border-slate-200 rounded-xl text-xs font-semibold p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                            Description
                        </label>
                        <textarea name="description"
                                  rows="3"
                                  placeholder="Outline session objectives, attire, or wellness facilitators..."
                                  class="w-full border border-slate-200 rounded-xl text-xs font-medium p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">{{ old('description') }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                        Publish Wellness Event →
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: All Wellness Events + Same-Page Downloadable QR Generator (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Card 1: All Wellness Events -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7">
                <div class="flex flex-wrap justify-between items-center gap-3 mb-5">
                    <div class="flex items-center gap-2.5">
                        <span class="w-1.5 h-5 bg-[#c99a2e] rounded-full"></span>
                        <h2 class="text-lg font-extrabold text-slate-900">
                            All Wellness Events ({{ $allEvents->count() }})
                        </h2>
                    </div>

                    <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl">
                        <button type="button"
                                onclick="filterEvents('all', this)"
                                class="event-filter-btn px-3 py-1.5 text-[11px] font-extrabold rounded-lg bg-white text-[#02338d] shadow-sm transition">
                            All ({{ $allEvents->count() }})
                        </button>
                        <button type="button"
                                onclick="filterEvents('upcoming', this)"
                                class="event-filter-btn px-3 py-1.5 text-[11px] font-extrabold rounded-lg text-slate-500 hover:text-slate-800 transition">
                            Upcoming ({{ $upcomingCount }})
                        </button>
                        <button type="button"
                                onclick="filterEvents('past', this)"
                                class="event-filter-btn px-3 py-1.5 text-[11px] font-extrabold rounded-lg text-slate-500 hover:text-slate-800 transition">
                            Past Events ({{ $pastCount }})
                        </button>
                    </div>
                </div>

                @if($allEvents->isEmpty())
                    <div class="border border-dashed border-slate-200 rounded-2xl p-10 text-center">
                        <p class="text-sm font-extrabold text-slate-700">No wellness sessions scheduled yet.</p>
                        <p class="text-xs text-slate-500 mt-1">Use the form on the left to create your first Strathmore WellLoop event.</p>
                    </div>
                @else
                    <div class="space-y-3.5 max-h-[430px] overflow-y-auto pr-1" id="hr-events-list">
                        @foreach($allEvents as $ev)
                            @php
                                $evStart       = $ev->starts_at ? $ev->starts_at->copy()->shiftTimezone($tz) : $now;
                                $evClosesAt    = $evStart->copy()->addMinutes($lateGraceMinutes);
                                $isUpcoming    = $ev->starts_at && $evStart->gt($cutoffTime);
                                $statusType    = $isUpcoming ? 'upcoming' : 'past';
                                $regCount      = (int) ($ev->registrations_count ?? $ev->registrations()->count());
                                $initial       = strtoupper(mb_substr(trim($ev->name), 0, 1));
                                $signedScanUrl = \Illuminate\Support\Facades\URL::signedRoute('checkin.scan', ['event' => $ev->id]);
                                $formattedMeta = $evStart->format('D, M d Y \a\t h:i A') . ' EAT';
                                $waLink        = $ev->whatsapp_channel_url;
                            @endphp

                            <div class="hr-event-row border border-slate-200/90 hover:border-slate-300 rounded-2xl p-5 transition bg-white shadow-[0_1px_3px_rgba(0,0,0,0.02)]"
                                 data-status="{{ $statusType }}">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-start gap-4 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl {{ $isUpcoming ? 'bg-[#e8f0fe] text-[#02338d]' : 'bg-slate-100 text-slate-500' }} font-extrabold text-base flex items-center justify-center shrink-0">
                                            {{ $initial }}
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="text-base font-extrabold text-slate-900">{{ $ev->name }}</h3>

                                                @if($isUpcoming)
                                                    <span class="text-[11px] font-extrabold px-3 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        Upcoming
                                                    </span>
                                                @else
                                                    <span class="text-[11px] font-extrabold px-3 py-0.5 rounded-full bg-slate-200/80 text-slate-600 border border-slate-300">
                                                        Past Event
                                                    </span>
                                                @endif

                                                @if(!empty($waLink))
                                                    <a href="{{ $waLink }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       title="Open Event WhatsApp Channel / Group"
                                                       class="inline-flex items-center gap-1 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                                                        <span>WhatsApp Linked ↗</span>
                                                    </a>
                                                @endif
                                            </div>

                                            <p class="text-xs text-slate-500 italic mt-1">
                                                {{ $evStart->format('D, M d Y \a\t h:i A') }} · {{ $ev->location }} · {{ $regCount }}/{{ $ev->capacity }} reg
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2.5 shrink-0">
                                        <a href="{{ route('events.edit', $ev) }}"
                                           class="text-xs font-extrabold bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl transition">
                                            Edit
                                        </a>

                                        @if($isUpcoming)
                                            <button type="button"
                                                    onclick="selectInlineQrEvent(this)"
                                                    data-event-id="{{ $ev->id }}"
                                                    data-event-name="{{ $ev->name }}"
                                                    data-event-date="{{ $formattedMeta }}"
                                                    data-event-location="{{ $ev->location }}"
                                                    data-event-points="{{ (int) ($ev->points_checkin ?? 25) }}"
                                                    data-scan-url="{{ $signedScanUrl }}"
                                                    data-closes-ts="{{ $evClosesAt->timestamp * 1000 }}"
                                                    class="text-xs font-extrabold bg-[#02338d] hover:bg-[#01225e] text-white px-5 py-2.5 rounded-xl shadow-sm transition">
                                                QR Code
                                            </button>
                                        @else
                                            <a href="{{ route('checkin.scan', $ev) }}"
                                               class="text-xs font-extrabold bg-[#1e293b] hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl shadow-sm transition">
                                                Attendance
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Card 2: Same-Page Venue QR Generator (Empty by Default; Only Shows Selected Active/Upcoming Session) -->
            @php
                $hasInitActiveQr = $initialQrEvent !== null;
                $initStart       = $hasInitActiveQr ? $initialQrEvent->starts_at->copy()->shiftTimezone($tz) : null;
                $initClosesTs    = $hasInitActiveQr ? ($initStart->copy()->addMinutes($lateGraceMinutes)->timestamp * 1000) : 0;
                $initDateStr     = $hasInitActiveQr ? ($initStart->format('D, M d Y \a\t h:i A') . ' EAT') : '';
                $initScanUrl     = $hasInitActiveQr ? \Illuminate\Support\Facades\URL::signedRoute('checkin.scan', ['event' => $initialQrEvent->id]) : '';
                $initPoints      = $hasInitActiveQr ? (int) ($initialQrEvent->points_checkin ?? 25) : 25;
            @endphp

            <!-- Empty State (Shown by Default until HR Clicks "QR Code" on an Active/Upcoming Session) -->
            <div id="inline-qr-empty-state"
                 class="{{ $hasInitActiveQr ? 'hidden' : '' }} bg-white rounded-3xl border border-dashed border-slate-300 p-7 text-center shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-[#e8f0fe] text-[#02338d] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-extrabold text-slate-800">Instant Venue QR Generator</h3>
                <p id="inline-qr-empty-message" class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    Click the <strong class="text-[#02338d]">QR Code</strong> button on any active or upcoming wellness session above to generate and download its venue check-in QR code.
                </p>
            </div>

            <!-- Active QR Card (Only Shown When an Active/Upcoming Session is Selected) -->
            <div id="inline-qr-card"
                 class="{{ $hasInitActiveQr ? '' : 'hidden' }} bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden transition-all"
                 data-current-name="{{ $hasInitActiveQr ? $initialQrEvent->name : '' }}"
                 data-current-date="{{ $initDateStr }}"
                 data-current-location="{{ $hasInitActiveQr ? $initialQrEvent->location : '' }}"
                 data-current-points="{{ $initPoints }}"
                 data-current-url="{{ $initScanUrl }}"
                 data-closes-ts="{{ $initClosesTs }}">

                <div class="bg-[#02338d] text-white px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#c99a2e] block">
                            Instant Venue QR Generator (Active & Upcoming Only)
                        </span>
                        <h3 id="inline-qr-title" class="text-base font-extrabold tracking-tight mt-0.5">
                            {{ $hasInitActiveQr ? $initialQrEvent->name : '' }}
                        </h3>
                        <p id="inline-qr-subtitle" class="text-xs text-blue-100 italic">
                            {{ $hasInitActiveQr ? ($initDateStr . ' • ' . $initialQrEvent->location) : '' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span id="inline-qr-points-badge" class="bg-[#c99a2e] text-[#01225e] text-xs font-extrabold px-3 py-1 rounded-full">
                            +{{ $initPoints }} Check-In Pts
                        </span>
                        <button type="button"
                                onclick="clearInlineQrGenerator()"
                                title="Close QR Preview"
                                class="bg-white/10 hover:bg-white/20 text-white text-xs font-extrabold px-2.5 py-1 rounded-lg transition">
                            ✕
                        </button>
                    </div>
                </div>

                <div class="p-6 flex flex-col sm:flex-row items-center gap-6">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 shrink-0">
                        <div id="inline-qrcode-canvas" class="bg-white p-3 rounded-xl shadow-sm inline-block"></div>
                    </div>

                    <div class="flex-1 w-full space-y-4">
                        <p class="text-xs text-slate-600 font-medium leading-relaxed">
                            Download this QR code to display on the projector or venue entrance door. Staff scan it with their phones to check in automatically.
                        </p>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                    Signed Check-In URL
                                </span>
                                <button type="button"
                                        onclick="copyInlineQrLink()"
                                        id="copy-qr-link-btn"
                                        class="text-[10px] font-extrabold text-[#02338d] hover:underline">
                                    Copy Link
                                </button>
                            </div>
                            <code id="inline-qr-url" class="text-[11px] text-[#02338d] font-mono break-all block bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                {{ $initScanUrl }}
                            </code>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                            <button type="button"
                                    onclick="downloadBrandedQrPoster()"
                                    class="bg-[#c99a2e] hover:bg-[#b58924] text-[#01225e] text-xs font-extrabold py-3 px-4 rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span>Download QR Poster (PNG)</span>
                            </button>

                            <button type="button"
                                    onclick="downloadRawQrPng()"
                                    class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3 px-4 rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span>Download QR Only (PNG)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
    function filterEvents(status, clickedBtn) {
        const rows = document.querySelectorAll('.hr-event-row');
        const buttons = document.querySelectorAll('.event-filter-btn');

        buttons.forEach(btn => {
            btn.classList.remove('bg-white', 'text-[#02338d]', 'shadow-sm');
            btn.classList.add('text-slate-500');
        });

        clickedBtn.classList.add('bg-white', 'text-[#02338d]', 'shadow-sm');
        clickedBtn.classList.remove('text-slate-500');

        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    function renderInlineQrCanvas(url) {
        const container = document.getElementById('inline-qrcode-canvas');
        if (!container || !url || typeof QRious !== 'function') return;

        container.innerHTML = '';
        const canvas = document.createElement('canvas');
        canvas.id = 'active-qr-canvas-el';
        container.appendChild(canvas);

        new QRious({
            element: canvas,
            value: url,
            size: 180,
            level: 'H',
            background: '#ffffff',
            foreground: '#02338d'
        });
    }

    function clearInlineQrGenerator(reasonText) {
        const card       = document.getElementById('inline-qr-card');
        const emptyState = document.getElementById('inline-qr-empty-state');
        const emptyMsg   = document.getElementById('inline-qr-empty-message');
        const container  = document.getElementById('inline-qrcode-canvas');

        if (card) {
            card.classList.add('hidden');
            card.dataset.currentUrl = '';
            card.dataset.closesTs   = '0';
        }
        if (container) container.innerHTML = '';
        if (emptyState) emptyState.classList.remove('hidden');
        if (reasonText && emptyMsg) {
            emptyMsg.textContent = reasonText;
        }
    }

    function selectInlineQrEvent(btn) {
        const card       = document.getElementById('inline-qr-card');
        const emptyState = document.getElementById('inline-qr-empty-state');
        if (!card) return;

        const closesTs = parseInt(btn.dataset.closesTs || '0', 10);
        if (closesTs > 0 && Date.now() > closesTs) {
            clearInlineQrGenerator('That session has already passed its 10-minute check-in window. QR codes are only generated for active and upcoming sessions.');
            window.location.reload();
            return;
        }

        const name     = btn.dataset.eventName;
        const date     = btn.dataset.eventDate;
        const location = btn.dataset.eventLocation;
        const points   = btn.dataset.eventPoints;
        const url      = btn.dataset.scanUrl;

        card.dataset.currentName     = name;
        card.dataset.currentDate     = date;
        card.dataset.currentLocation = location;
        card.dataset.currentPoints   = points;
        card.dataset.currentUrl      = url;
        card.dataset.closesTs        = String(closesTs);

        document.getElementById('inline-qr-title').textContent        = name;
        document.getElementById('inline-qr-subtitle').textContent     = `${date} • ${location}`;
        document.getElementById('inline-qr-points-badge').textContent = `+${points} Check-In Pts`;
        document.getElementById('inline-qr-url').textContent          = url;

        if (emptyState) emptyState.classList.add('hidden');
        card.classList.remove('hidden');

        renderInlineQrCanvas(url);

        card.classList.add('ring-2', 'ring-[#02338d]');
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        setTimeout(() => card.classList.remove('ring-2', 'ring-[#02338d]'), 1400);
    }

    function copyInlineQrLink() {
        const card = document.getElementById('inline-qr-card');
        const btn  = document.getElementById('copy-qr-link-btn');
        if (!card || !navigator.clipboard || !card.dataset.currentUrl) return;

        navigator.clipboard.writeText(card.dataset.currentUrl).then(() => {
            btn.textContent = 'Copied ✓';
            setTimeout(() => { btn.textContent = 'Copy Link'; }, 2000);
        });
    }

    function slugifyFileName(text) {
        return (text || 'wellness-event')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    }

    function downloadRawQrPng() {
        const card = document.getElementById('inline-qr-card');
        if (!card || !card.dataset.currentUrl || typeof QRious !== 'function') return;

        const exportCanvas = document.createElement('canvas');
        new QRious({
            element: exportCanvas,
            value: card.dataset.currentUrl,
            size: 600,
            level: 'H',
            background: '#ffffff',
            foreground: '#02338d'
        });

        const link = document.createElement('a');
        link.download = `Strathmore-WellLoop-QR-${slugifyFileName(card.dataset.currentName)}.png`;
        link.href = exportCanvas.toDataURL('image/png');
        link.click();
    }

    function downloadBrandedQrPoster() {
        const card = document.getElementById('inline-qr-card');
        if (!card || !card.dataset.currentUrl || typeof QRious !== 'function') return;

        const name     = card.dataset.currentName || 'Wellness Session';
        const date     = card.dataset.currentDate || '';
        const location = card.dataset.currentLocation || '';
        const points   = card.dataset.currentPoints || '25';
        const url      = card.dataset.currentUrl;

        const poster = document.createElement('canvas');
        poster.width  = 1200;
        poster.height = 1500;
        const ctx = poster.getContext('2d');

        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, 1200, 1500);

        ctx.fillStyle = '#02338d';
        ctx.fillRect(0, 0, 1200, 270);

        ctx.fillStyle = '#c99a2e';
        ctx.fillRect(0, 270, 1200, 16);

        ctx.fillStyle = '#c99a2e';
        ctx.font = '800 24px Montserrat, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('STRATHMORE UNIVERSITY • PEOPLE & CULTURE', 600, 95);

        ctx.fillStyle = '#ffffff';
        ctx.font = '800 54px Montserrat, sans-serif';
        ctx.fillText('Official Venue Check-In', 600, 175);

        ctx.fillStyle = '#dbeafe';
        ctx.font = '600 24px Montserrat, sans-serif';
        ctx.fillText('Scan with your phone camera to record attendance (+' + points + ' pts)', 600, 225);

        ctx.fillStyle = '#0f172a';
        ctx.font = '800 44px Montserrat, sans-serif';
        ctx.fillText(name.length > 42 ? name.substring(0, 42) + '...' : name, 600, 380);

        ctx.fillStyle = '#475569';
        ctx.font = '600 28px Montserrat, sans-serif';
        ctx.fillText(date + '  •  ' + location, 600, 435);

        ctx.strokeStyle = '#cbd5e1';
        ctx.lineWidth = 4;
        ctx.strokeRect(250, 490, 700, 700);

        const qrCanvas = document.createElement('canvas');
        new QRious({
            element: qrCanvas,
            value: url,
            size: 620,
            level: 'H',
            background: '#ffffff',
            foreground: '#02338d'
        });
        ctx.drawImage(qrCanvas, 290, 530);

        ctx.fillStyle = '#02338d';
        ctx.font = '800 28px Montserrat, sans-serif';
        ctx.fillText('Gate Opens 1 Hour Before Start • Closes 10 Minutes After Start', 600, 1280);

        ctx.fillStyle = '#64748b';
        ctx.font = '500 22px Montserrat, sans-serif';
        ctx.fillText('WellLoop Wellness Performance Management Information System (WPMIS)', 600, 1340);

        const link = document.createElement('a');
        link.download = `Strathmore-Poster-${slugifyFileName(name)}.png`;
        link.href = poster.toDataURL('image/png');
        link.click();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const card = document.getElementById('inline-qr-card');
        if (card && card.dataset.currentUrl) {
            renderInlineQrCanvas(card.dataset.currentUrl);
        }

        // Auto-clear QR preview if the selected session passes its 10-minute cutoff while page is open
        setInterval(function () {
            if (!card || card.classList.contains('hidden')) return;
            const closesTs = parseInt(card.dataset.closesTs || '0', 10);
            if (closesTs > 0 && Date.now() > closesTs) {
                clearInlineQrGenerator('Session check-in window has closed. Select another upcoming session above.');
            }
        }, 15000);

        if (typeof flatpickr !== 'function') return;

        function clampSelectedDateToCurrentTime(selectedDates, instance) {
            const now = new Date();
            now.setSeconds(0, 0);

            if (!selectedDates || selectedDates.length === 0) {
                instance.setDate(now, false);
                if (instance.altInput) {
                    instance.altInput.value = instance.formatDate(now, instance.config.altFormat);
                }
                instance.input.value = instance.formatDate(now, instance.config.dateFormat);
                return;
            }

            const picked = selectedDates[0];
            const isToday =
                picked.getFullYear() === now.getFullYear() &&
                picked.getMonth() === now.getMonth() &&
                picked.getDate() === now.getDate();

            if (isToday) {
                const minH = String(now.getHours()).padStart(2, '0');
                const minM = String(now.getMinutes()).padStart(2, '0');
                instance.set('minTime', `${minH}:${minM}`);
            } else {
                instance.set('minTime', null);
            }

            if (picked.getTime() < now.getTime()) {
                instance.setDate(now, false);
                if (instance.altInput) {
                    instance.altInput.value = instance.formatDate(now, instance.config.altFormat);
                }
                instance.input.value = instance.formatDate(now, instance.config.dateFormat);

                const notice = document.getElementById('datetime-clamp-notice');
                if (notice) {
                    notice.classList.remove('hidden');
                    clearTimeout(window.__clampNoticeTimer);
                    window.__clampNoticeTimer = setTimeout(() => notice.classList.add('hidden'), 4000);
                }
            }
        }

        const nowInit = new Date();
        nowInit.setSeconds(0, 0);

        flatpickr('#create-datetime-picker', {
            wrap: true,
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            altInput: true,
            altFormat: 'D, M j, Y \\a\\t h:i K',
            time_24hr: false,
            minDate: 'today',
            defaultDate: '{{ old('starts_at') }}' || nowInit,
            defaultHour: nowInit.getHours(),
            defaultMinute: nowInit.getMinutes(),
            minuteIncrement: 5,
            allowInput: false,
            clickOpens: true,
            onReady: function (selectedDates, dateStr, instance) {
                clampSelectedDateToCurrentTime(selectedDates, instance);
            },
            onOpen: function (selectedDates, dateStr, instance) {
                clampSelectedDateToCurrentTime(selectedDates, instance);
            },
            onChange: function (selectedDates, dateStr, instance) {
                clampSelectedDateToCurrentTime(selectedDates, instance);
            },
            onClose: function (selectedDates, dateStr, instance) {
                clampSelectedDateToCurrentTime(selectedDates, instance);
            }
        });
    });
</script>
@endpush