@extends('layouts.app')

@section('content')
@php
    $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 10);
    $now              = now($tz);
@endphp

<div class="max-w-7xl mx-auto space-y-8">

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-extrabold border border-emerald-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-xs font-extrabold border border-rose-200 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
                Strathmore Staff Wellness
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">Upcoming Wellness Sessions</h1>
            <p class="text-xs text-slate-500 mt-1">Register for upcoming campus sessions across the 8 dimensions of wellness.</p>
        </div>

        <form method="GET" action="{{ route('events.index') }}" class="flex items-center gap-2 w-full md:w-auto">
            <input type="text"
                   name="search"
                   value="{{ $search ?? '' }}"
                   placeholder="Search events or venues..."
                   class="w-full md:w-64 border border-slate-200 rounded-xl text-xs font-semibold px-4 py-2.5 bg-white focus:ring-[#02338d] focus:border-[#02338d]">
            <button type="submit" class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-4 py-2.5 rounded-xl transition">
                Search
            </button>
        </form>
    </div>

    <!-- Upcoming Events Grid -->
    @if($upcomingEvents->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-10 text-center shadow-sm">
            <p class="text-sm font-extrabold text-slate-700">No upcoming wellness sessions scheduled right now.</p>
            <p class="text-xs text-slate-500 mt-1">Check back soon or review past sessions below.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($upcomingEvents as $event)
                @php
                    $eventStart  = $event->starts_at->copy()->shiftTimezone($tz);
                    $myReg       = $myRegistrations->get($event->id);
                    $spotsLeft   = max(0, (int) $event->capacity - (int) ($event->registrations_count ?? 0));
                    $whatsappUrl = $event->whatsapp_channel_url;
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#02338d] bg-[#e8f0fe] px-2.5 py-1 rounded-full">
                                {{ strtoupper($event->category) }}
                            </span>
                            <span class="text-[11px] font-extrabold text-[#c99a2e] bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                +{{ ($event->points_checkin ?? 25) + ($event->points_feedback ?? 10) }} pts
                            </span>
                        </div>

                        <h2 class="text-lg font-extrabold text-slate-900">{{ $event->name }}</h2>
                        <p class="text-xs text-slate-500 italic mt-1 line-clamp-2">
                            {{ $event->description ?? 'Upcoming Strathmore University staff wellness session.' }}
                        </p>

                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-1.5 text-xs text-slate-600">
                            <p class="font-bold">{{ $eventStart->format('D, d M Y \a\t h:i A') }} EAT</p>
                            <p class="italic text-slate-500">{{ $event->location }}</p>
                            <p class="text-[11px] font-semibold text-slate-500">
                                <strong>{{ $spotsLeft }}</strong> of {{ $event->capacity }} spots remaining
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        @if($myReg)
                            @if($myReg->checkIn !== null)
                                <div class="w-full text-center bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-extrabold py-3 rounded-xl">
                                    Checked In ✓
                                </div>
                            @elseif(! $myReg->waiver && ! $myReg->consent_given)
                                <a href="{{ route('registrations.waiver', $myReg) }}"
                                   class="w-full block text-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-extrabold py-3.5 rounded-xl transition">
                                    Complete Waiver →
                                </a>
                            @else
                                <a href="{{ route('registrations.ticket', $myReg) }}"
                                   class="w-full block text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold py-3.5 rounded-xl transition">
                                    View Official Event Pass →
                                </a>
                            @endif

                            @if(!empty($whatsappUrl))
                                <a href="{{ $whatsappUrl }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="w-full block text-center bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-[11px] font-extrabold py-2.5 rounded-xl transition">
                                    Join Event WhatsApp Channel ↗
                                </a>
                            @endif
                        @elseif($spotsLeft <= 0)
                            <button type="button" disabled class="w-full bg-slate-100 text-slate-400 text-xs font-extrabold py-3.5 rounded-xl cursor-not-allowed">
                                Session Full
                            </button>
                        @else
                            <a href="{{ route('events.register', $event) }}"
                               class="w-full block text-center bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                                Register for Event →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Past Wellness Sessions (Fixes Photo 1: Explicit Badge for Every Single Card) -->
    @if(isset($pastEvents) && $pastEvents->isNotEmpty())
        <div class="mt-12 pt-8 border-t border-slate-200">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-2.5">
                    <span class="w-1.5 h-6 bg-slate-400 rounded-full"></span>
                    <h2 class="text-lg font-extrabold text-slate-800">Past Wellness Sessions</h2>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-[10px] font-extrabold">
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Attended ✓ = Checked In</span>
                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">Registered • Missed = No Check-In</span>
                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200">Not Registered = Did Not Sign Up</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pastEvents as $pastEv)
                    @php
                        $pastStart = $pastEv->starts_at->copy()->shiftTimezone($tz);
                        $myPastReg = $myRegistrations->get($pastEv->id);
                        $attended  = $myPastReg && ($myPastReg->checkIn !== null || in_array($myPastReg->status, ['checked_in', 'completed'], true));
                    @endphp
                    <div class="bg-white/90 rounded-3xl border border-slate-200 p-6 flex flex-col justify-between shadow-sm">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full">
                                    {{ strtoupper($pastEv->category) }} • Past Record
                                </span>

                                @if($attended)
                                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Attended ✓
                                    </span>
                                @elseif($myPastReg)
                                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                        Registered • Missed
                                    </span>
                                @else
                                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                        Not Registered
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-base font-extrabold text-slate-800">{{ $pastEv->name }}</h3>
                            <p class="text-xs text-slate-500 italic mt-1">
                                {{ $pastStart->format('D, d M Y \a\t h:i A') }} EAT • {{ $pastEv->location }}
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-400">
                                {{ $pastEv->registrations_count ?? 0 }}/{{ $pastEv->capacity }} registered
                            </span>

                            @if($attended && $myPastReg->status !== 'completed' && $myPastReg->feedback === null)
                                <a href="{{ route('feedback.create', $myPastReg) }}"
                                   class="text-xs font-extrabold text-[#02338d] hover:underline">
                                    Complete Post-WHO →
                                </a>
                            @elseif($attended)
                                <span class="text-[11px] font-extrabold text-emerald-600">Feedback Completed ✓</span>
                            @else
                                <span class="text-[11px] font-extrabold text-slate-400">Session Ended</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection