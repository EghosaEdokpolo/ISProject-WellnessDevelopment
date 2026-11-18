@extends('layouts.app')

@section('content')
@php
    $tz         = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $eventStart = $event->starts_at ? $event->starts_at->copy()->shiftTimezone($tz) : now($tz);
    $eventEnd   = $eventStart->copy()->addHours(2);

    // Resolve and normalize WhatsApp URL from either column
    $rawWhatsapp = trim((string) ($event->whatsapp_channel_url ?? $event->comlink ?? ''));
    if ($rawWhatsapp !== '' && ! preg_match('~^https?://~i', $rawWhatsapp)) {
        $rawWhatsapp = 'https://' . ltrim($rawWhatsapp, '/');
    }
    $whatsappUrl = $rawWhatsapp !== '' ? $rawWhatsapp : null;

    // Build RFC-3986 Google Calendar Deep Link in Africa/Nairobi Time
    $startLocal = $eventStart->format('Ymd\THis');
    $endLocal   = $eventEnd->format('Ymd\THis');

    $calendarDetails = trim(
        ($event->description ?: 'Strathmore University Staff Wellness Session.')
        . "\n\nStaff ID: " . (auth()->user()->staff_id ?? '')
        . "\nVenue: " . $event->location
        . "\nCheck-In Gate: Opens 1 hour before start and closes 10 minutes after start (EAT)."
    );

    $gcalQuery = http_build_query([
        'action'   => 'TEMPLATE',
        'text'     => 'Strathmore WellLoop: ' . $event->name,
        'dates'    => $startLocal . '/' . $endLocal,
        'details'  => $calendarDetails,
        'location' => $event->location . ', Strathmore University, Nairobi',
        'ctz'      => $tz,
    ], '', '&', PHP_QUERY_RFC3986);

    $googleCalendarUrl = 'https://calendar.google.com/calendar/render?' . $gcalQuery;
@endphp

<div class="max-w-2xl mx-auto px-4 py-6 space-y-6">

    <!-- Top Back Link & Staff ID Badge -->
    <div class="flex items-center justify-between">
        <a href="{{ route('events.index') }}"
           class="text-xs font-extrabold text-slate-500 hover:text-[#02338d] transition inline-flex items-center gap-1.5">
            ← Back to Events Hub
        </a>
        <span class="text-[11px] font-extrabold text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
            Staff ID: {{ auth()->user()->staff_id }}
        </span>
    </div>

    <!-- Main Confirmation Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Navy Institutional Header -->
        <div class="bg-[#02338d] text-white px-6 py-7 text-center">
            <div class="w-12 h-12 rounded-2xl bg-white/10 text-[#c99a2e] flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#c99a2e] block">
                Registration & Waiver Confirmed (+5 Pts)
            </span>
            <h1 class="text-2xl font-extrabold tracking-tight mt-1">{{ $event->name }}</h1>
            <p class="text-xs text-blue-100 italic mt-1">
                {{ $eventStart->format('l, d M Y \a\t h:i A') }} EAT • {{ $event->location }}
            </p>
        </div>

        <div class="p-6 sm:p-8 space-y-5">

            <!-- CARD 1: WhatsApp Session Community Integration -->
            <div class="rounded-2xl border {{ $whatsappUrl ? 'border-emerald-200 bg-emerald-50/60' : 'border-slate-200 bg-slate-50' }} p-5">
                <span class="text-[10px] font-extrabold uppercase tracking-widest {{ $whatsappUrl ? 'text-emerald-700' : 'text-slate-400' }} block">
                    Session Community • WhatsApp Channel
                </span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-1">
                    Join the Event WhatsApp Channel
                </h2>
                <p class="text-xs text-slate-600 italic mt-1 leading-relaxed">
                    Receive real-time venue updates, facilitator notes, and wellness preparation reminders on WhatsApp.
                </p>

                @if($whatsappUrl)
                    <a href="{{ $whatsappUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="mt-4 w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-extrabold py-3.5 px-5 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Open WhatsApp Channel / Group ↗</span>
                    </a>
                @else
                    <div class="mt-4 w-full bg-white border border-slate-200 text-slate-500 text-xs font-bold py-3 px-4 rounded-xl text-center">
                        No WhatsApp channel link attached to this event yet.
                    </div>
                @endif
            </div>

            <!-- CARD 2: Calendar Integration (Single Clean "Add to Calendar" Button) -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] block">
                    Calendar Integration
                </span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-1">
                    Book Event on Your Calendar
                </h2>
                <p class="text-xs text-slate-500 italic mt-1 leading-relaxed">
                    Save the session date, venue, and reminder directly to Google Calendar.
                </p>

                <a href="{{ $googleCalendarUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="mt-4 w-full bg-[#02338d] hover:bg-[#01225e] text-white text-xs sm:text-sm font-extrabold py-3.5 px-5 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Add to Calendar</span>
                </a>
            </div>

            <!-- CARD 3: Official Event Pass & Venue Check-In -->
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <a href="{{ route('registrations.ticket', $registration) }}"
                   class="w-full sm:w-auto flex-1 bg-[#c99a2e] hover:bg-[#b58924] text-[#01225e] text-xs sm:text-sm font-extrabold py-3.5 px-6 rounded-xl shadow-sm transition text-center">
                    Open Official Event Pass & QR Scanner →
                </a>

                <a href="{{ route('events.index') }}"
                   class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold py-3.5 px-5 rounded-xl transition text-center">
                    Done
                </a>
            </div>

        </div>
    </div>
</div>
@endsection