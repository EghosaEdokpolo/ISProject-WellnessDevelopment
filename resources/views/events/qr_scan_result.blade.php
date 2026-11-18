@extends('layouts.app')

@section('content')
@php
    $tz = (string) config('wellloop.timezone', 'Africa/Nairobi');
@endphp

<div class="max-w-lg mx-auto px-4 py-8">
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm text-center">

        @if(!empty($scanError))
            <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                </svg>
            </div>

            <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-800 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full">
                Check-In Unavailable
            </span>

            <h1 class="text-2xl font-extrabold text-slate-900 mt-3">{{ $event->name }}</h1>
            <p class="text-sm text-slate-600 mt-3 leading-relaxed">{{ $scanError }}</p>

            <div class="mt-6">
                <a href="{{ route('events.index') }}"
                   class="block text-center bg-[#02338d] hover:bg-[#01225e] text-white font-bold py-3.5 rounded-xl text-xs transition">
                    Return to Events Hub
                </a>
            </div>
        @else
            <div class="w-16 h-16 {{ !empty($isLate) ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }} rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <span class="text-[10px] font-extrabold uppercase tracking-widest {{ !empty($isLate) ? 'text-amber-800 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200' }} border px-3 py-1 rounded-full">
                @if($autoRegistered)
                    Walk-In Registered & Gate Entered
                @elseif($alreadyCheckedIn)
                    Already Checked In
                @elseif(!empty($isLate))
                    Gate Entered • Late Grace ({{ $minutesLate }}m Late)
                @else
                    Gate Entered • On-Time Attendance
                @endif
            </span>

            <h1 class="text-2xl font-extrabold text-slate-900 mt-3">{{ $event->name }}</h1>
            <p class="text-xs text-slate-500 mt-1">
                Verified at {{ optional($registration?->checkIn?->checked_in_at)?->copy()->setTimezone($tz)->format('H:i \o\n d M Y') }} EAT • {{ auth()->user()->name }} ({{ auth()->user()->staff_id }})
            </p>

            @if($pointsEarned > 0)
                @if(!empty($isLate) && ($pointsReducedBy ?? 0) > 0)
                    <div class="mt-6 bg-amber-50 border border-amber-300 rounded-2xl p-4 text-left space-y-2">
                        <div class="flex items-center justify-between text-xs font-extrabold text-amber-950">
                            <span>Late Grace Points Summary</span>
                            <span class="bg-[#c99a2e] text-[#01225e] px-3 py-1 rounded-lg">+{{ $pointsEarned }} pts Credited</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-amber-200 text-center text-[11px]">
                            <div class="bg-white/80 rounded-xl p-2">
                                <span class="block text-slate-400 font-bold">On-Time Max</span>
                                <span class="font-extrabold text-slate-700">{{ $basePoints }} pts</span>
                            </div>
                            <div class="bg-rose-50 rounded-xl p-2">
                                <span class="block text-rose-600 font-bold">Late Reduction</span>
                                <span class="font-extrabold text-rose-700">-{{ $pointsReducedBy }} pts</span>
                            </div>
                            <div class="bg-emerald-50 rounded-xl p-2">
                                <span class="block text-emerald-700 font-bold">Final Earned</span>
                                <span class="font-extrabold text-emerald-800">+{{ $pointsEarned }} pts</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold px-5 py-3.5 rounded-2xl text-xs flex justify-between items-center">
                        <span>Full on-time check-in points credited:</span>
                        <span class="bg-emerald-600 text-white px-3 py-1 rounded-lg font-extrabold">+{{ $pointsEarned }} pts</span>
                    </div>
                @endif
            @endif

            @if($needsOnboarding && $registration)
                <div class="mt-6 bg-[#e8f0fe] border border-[#02338d]/20 rounded-2xl p-4 text-left">
                    <p class="text-xs font-extrabold text-[#02338d]">Quick Step Remaining (+5 pts)</p>
                    <p class="text-xs text-slate-600 mt-1">
                        Since you registered at the venue via QR scan, complete your 30-second Pre-Event WHO-5 baseline & digital waiver.
                    </p>
                    <a href="{{ route('registrations.who5', $registration) }}"
                       class="mt-3 block text-center bg-[#02338d] hover:bg-[#01225e] text-white font-bold py-3 rounded-xl text-xs transition">
                        Complete Baseline & Waiver →
                    </a>
                </div>
            @elseif($registration)
                <div class="mt-6 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('events.index') }}"
                       class="flex-1 block text-center bg-[#02338d] hover:bg-[#01225e] text-white font-bold py-3.5 rounded-xl text-xs transition">
                        Enter Session / Events Hub
                    </a>
                    <a href="{{ route('feedback.create', $registration) }}"
                       class="flex-1 block text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl text-xs transition">
                        Post-Event Feedback
                    </a>
                </div>
            @endif
        @endif

    </div>
</div>
@endsection