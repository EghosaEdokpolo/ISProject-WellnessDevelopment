@extends('layouts.app')

@section('content')
@php
    $gcalStart = $event->starts_at->copy()->utc()->format('Ymd\THis\Z');
    $gcalEnd   = $event->starts_at->copy()->addHour()->utc()->format('Ymd\THis\Z');

    $calendarDetails = trim(
        ($event->description ?? 'Strathmore University Staff Wellness Session') .
        "\n\nPass Code: WL-" . str_pad((string) $registration->id, 5, '0', STR_PAD_LEFT) .
        "\nOfficial Event Pass: " . route('registrations.ticket', $registration)
    );

    $googleCalendarUrl = 'https://calendar.google.com/calendar/render?' . http_build_query([
        'action'   => 'TEMPLATE',
        'text'     => $event->name . ' — Strathmore WellLoop',
        'dates'    => $gcalStart . '/' . $gcalEnd,
        'details'  => $calendarDetails,
        'location' => $event->location,
    ]);
@endphp

<div class="max-w-2xl mx-auto px-4 py-10">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Top Strathmore Blue-to-Gold Accent Bar -->
        <div class="h-2 bg-gradient-to-r from-[#02338d] via-[#02338d] to-[#c99a2e]"></div>

        <div class="p-8 sm:p-10 text-center">
            <!-- Green Check Icon -->
            <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-5">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Status Pill -->
            <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3.5 py-1 rounded-full">
                Registration & Waiver Confirmed (+5 Pts)
            </span>

            <!-- Event Title & Schedule -->
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-4 tracking-tight">
                You're Confirmed for {{ $event->name }}!
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 italic mt-2">
                {{ $event->starts_at->format('l, d M Y \a\t H:i') }} • {{ $event->location }}
            </p>

            <!-- Step 1: Schedule Reminder Box -->
            <div class="mt-8 bg-slate-50/70 border border-slate-200 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-left">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#02338d] block">
                        Step 1 · Schedule Reminder
                    </span>
                    <h2 class="text-base font-extrabold text-slate-900 mt-0.5">Book Event on Your Calendar</h2>
                    <p class="text-xs text-slate-500 italic mt-0.5">
                        Save the session date, venue, and reminder directly to Google Calendar.
                    </p>
                </div>

                <button type="button"
                        id="add-to-calendar-btn"
                        onclick="openCalendarAndReturnToEvents(@json($googleCalendarUrl), @json(route('events.index')))"
                        class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-5 py-3.5 rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span id="add-to-calendar-label">Add to Calendar</span>
                </button>
            </div>

            <!-- Footer Navigation Links -->
            <div class="mt-8 pt-5 border-t border-slate-100 flex items-center justify-between text-xs font-extrabold">
                <a href="{{ route('registrations.ticket', $registration) }}" class="text-[#02338d] hover:underline">
                    View Official Event Pass →
                </a>
                <a href="{{ route('events.index') }}" class="text-slate-500 hover:text-slate-800 transition">
                    Return to Wellness Events
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCalendarAndReturnToEvents(calendarUrl, eventsPageUrl) {
        const btn = document.getElementById('add-to-calendar-btn');
        const label = document.getElementById('add-to-calendar-label');

        if (btn && label) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-wait');
            label.innerText = 'Opening Calendar...';
        }

        // Open Google Calendar in a new tab so the user can save the reminder
        window.open(calendarUrl, '_blank', 'noopener,noreferrer');

        // Automatically return this tab back to the Events hub
        setTimeout(function () {
            window.location.href = eventsPageUrl;
        }, 700);
    }
</script>
@endpush