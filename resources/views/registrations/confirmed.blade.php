@extends('layouts.app')

@section('content')
@php
    $event = $event ?? $registration->event;
    $calendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
        . '&text=' . rawurlencode($event->name)
        . '&dates=' . $event->starts_at->format('Ymd\THis') . '/' . $event->starts_at->copy()->addHours(2)->format('Ymd\THis')
        . '&details=' . rawurlencode(($event->description ?? '') . ($event->comlink ? "\n\nWhatsApp Channel: " . $event->comlink : ''))
        . '&location=' . rawurlencode($event->location ?? 'Strathmore University');
@endphp

<div class="max-w-2xl mx-auto px-6 py-10">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-[#02338d] via-[#02338d] to-[#c99a2e]"></div>

        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <span class="text-[11px] font-bold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
                Registration & Waiver Confirmed (+5 pts)
            </span>

            <h1 class="text-2xl font-extrabold text-slate-900 mt-3">You're Confirmed for {{ $event->name }}!</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $event->starts_at->format('l, d M Y \a\t H:i') }} • {{ $event->location }}
            </p>

            <!-- Step-by-Step Post-Registration Actions -->
            <div class="mt-8 space-y-4 text-left">

                <!-- Step 1: Book on Calendar -->
                <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#02338d]">Step 1 • Schedule Reminder</span>
                        <h2 class="text-base font-bold text-slate-900">Book Event on Your Calendar</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Save the session date, venue, and reminder directly to Google Calendar.</p>
                    </div>
                    <a href="{{ $calendarUrl }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 bg-[#02338d] text-white text-xs font-bold px-5 py-3 rounded-xl hover:bg-[#01225e] transition shrink-0 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Add to Calendar
                    </a>
                </div>

                <!-- Step 2: Join Admin-Configured WhatsApp Channel -->
                @if($event->comlink)
                    <div class="p-5 rounded-2xl border border-emerald-200 bg-emerald-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">Step 2 • Live Event Updates</span>
                            <h2 class="text-base font-bold text-slate-900">Join Event WhatsApp Channel</h2>
                            <p class="text-xs text-slate-600 mt-0.5">Get session updates, facilitator notes, and venue alerts from People & Culture.</p>
                        </div>
                        <a href="{{ $event->comlink }}" target="_blank" rel="noopener noreferrer" id="whatsapp-channel-btn"
                           class="inline-flex items-center justify-center gap-2 bg-emerald-600 text-white text-xs font-bold px-5 py-3 rounded-xl hover:bg-emerald-700 transition shrink-0 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Open WhatsApp Channel
                        </a>
                    </div>
                @endif

            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('registrations.ticket', $registration) }}" class="text-xs font-bold text-[#02338d] hover:underline">
                    View Official Event Pass →
                </a>
                <a href="{{ route('events.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                    Return to Wellness Events
                </a>
            </div>
        </div>
    </div>
</div>
@endsection