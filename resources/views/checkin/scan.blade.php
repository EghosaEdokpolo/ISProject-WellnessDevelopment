@extends('layouts.app')

@section('content')
@php
    $tz           = $tz ?? (string) config('wellloop.timezone', 'Africa/Nairobi');
    $eventStart   = $event->starts_at->copy()->shiftTimezone($tz);
    $capacity     = max(1, (int) ($event->capacity ?? 80));
    $pct          = min(100, (int) round(($checkedInCount / $capacity) * 100));
    $gateStatus   = $gate['status'] ?? 'locked';
    $rows         = $registrations ?? collect();
@endphp

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap">
<style>
    .su-brand-scope, .su-brand-scope * {
        font-family: 'Montserrat', sans-serif;
    }
</style>

<div class="su-brand-scope max-w-6xl mx-auto space-y-6">

    <!-- Top Navigation & Live EAT Clock -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('events.management') }}"
           class="text-xs font-extrabold text-[#02338d] hover:underline inline-flex items-center gap-1.5">
            ← Back to Event Management
        </a>

        <div class="flex items-center gap-2 bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-sm text-xs font-bold text-slate-700">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span id="live-eat-clock">{{ now($tz)->format('D, d M Y • H:i:s') }} EAT</span>
        </div>
    </div>

    <!-- Session Header Banner -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-1.5 bg-gradient-to-r from-[#02338d] via-[#02338d] to-[#c99a2e]"></div>
        <div class="p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
                        {{ strtoupper($event->category) }} • Session Attendance Monitor
                    </span>

                    @if($gateStatus === 'open_on_time')
                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Gate Open (On Time)
                        </span>
                    @elseif($gateStatus === 'late_grace')
                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                            10-Min Late Grace Active
                        </span>
                    @elseif($gateStatus === 'locked')
                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                            Upcoming • Gate Locked
                        </span>
                    @else
                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                            Session Concluded • Gate Closed
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $event->name }}</h1>
                <p class="text-xs text-slate-500 italic mt-1">
                    {{ $eventStart->format('l, d M Y \a\t H:i') }} EAT • {{ $event->location }}
                </p>
            </div>

            <!-- KPI Metrics -->
            <div class="grid grid-cols-3 gap-3 shrink-0">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-center">
                    <span class="block text-[10px] font-extrabold uppercase text-slate-400">Registered</span>
                    <span class="text-xl font-extrabold text-slate-900">{{ $registeredCount }}</span>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-3 text-center">
                    <span class="block text-[10px] font-extrabold uppercase text-emerald-700">Checked In</span>
                    <span class="text-xl font-extrabold text-emerald-800">{{ $checkedInCount }}</span>
                </div>
                <div class="bg-[#e8f0fe] border border-blue-200 rounded-2xl px-4 py-3 text-center">
                    <span class="block text-[10px] font-extrabold uppercase text-[#02338d]">Turnout</span>
                    <span class="text-xl font-extrabold text-[#02338d]">{{ $pct }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendee Check-In & WHO-5 Completion Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Staff Attendance & WHO-5 Status</h2>
                <p class="text-xs text-slate-500">Verified gate entries, check-in timestamps (EAT), and Post-WHO completion status.</p>
            </div>

            @if($gateStatus !== 'closed')
                <a href="{{ route('events.qr.poster', $event) }}"
                   target="_blank"
                   class="text-xs font-extrabold bg-[#c99a2e] hover:bg-yellow-500 text-[#01225e] px-4 py-2.5 rounded-xl shadow-sm transition">
                    Open Venue QR Poster →
                </a>
            @endif
        </div>

        @if($rows->isEmpty())
            <div class="p-10 text-center">
                <p class="text-sm font-extrabold text-slate-700">No registrations recorded for this session.</p>
                <p class="text-xs text-slate-500 mt-1">Staff registrations and venue QR scans will appear here automatically.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-6">Staff Member</th>
                            <th class="py-3.5 px-4">Staff ID</th>
                            <th class="py-3.5 px-4">Check-In Status</th>
                            <th class="py-3.5 px-4">Verified At (EAT)</th>
                            <th class="py-3.5 px-6 text-right">Post-WHO Feedback</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($rows as $reg)
                            @php
                                $attendee   = $reg->user;
                                $checkedIn  = $reg->checkIn !== null;
                                $postDone   = $reg->status === 'completed' || $reg->feedback !== null;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-6 font-extrabold text-slate-900">
                                    {{ $attendee?->name ?? 'Unknown Staff' }}
                                    <span class="block text-[11px] font-normal text-slate-400">{{ $attendee?->department ?? 'Strathmore University' }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-700">
                                    {{ $attendee?->staff_id ?? '—' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($checkedIn)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Checked In ({{ strtoupper($reg->checkIn->method ?? 'QR') }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                            Absent / Not Scanned
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-600">
                                    @if($checkedIn)
                                        {{ $reg->checkIn->checked_in_at->copy()->shiftTimezone($tz)->format('d M Y, H:i:s') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    @if($postDone)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-[#02338d] border border-blue-200">
                                            Submitted ✓
                                        </span>
                                    @elseif($checkedIn)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                            Pending
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const clockEl = document.getElementById('live-eat-clock');
        if (!clockEl) return;

        setInterval(function () {
            const now = new Date();
            const formatted = now.toLocaleString('en-GB', {
                timeZone: 'Africa/Nairobi',
                weekday: 'short',
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).replace(',', ' •');
            clockEl.textContent = formatted + ' EAT';
        }, 1000);
    });
</script>
@endpush