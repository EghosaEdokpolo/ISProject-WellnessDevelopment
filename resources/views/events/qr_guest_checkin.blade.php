@extends('layouts.app')

@section('content')
@php
    $tz           = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $eventStart   = $gate['starts_at'];
    $gateOpensAt  = $gate['opens_at'];
    $gateClosesAt = $gate['closes_at'];
    $isGateOpen   = (bool) ($gate['is_open'] ?? false);
    $isLateGrace  = ($gate['status'] ?? '') === 'late_grace';
    $isTooEarly   = ($gate['status'] ?? '') === 'locked';
@endphp

<div class="max-w-md mx-auto px-4 py-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden">
        <!-- Navy Header -->
        <div class="bg-[#02338d] text-white px-6 py-7 text-center">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#c99a2e] block">
                Strathmore University • Quick Venue Check-In
            </span>
            <h1 class="text-xl font-extrabold tracking-tight mt-1">{{ $event->name }}</h1>
            <p class="text-xs text-blue-100 italic mt-1">
                {{ $eventStart->format('l, d M Y \a\t h:i A') }} EAT • {{ $event->location }}
            </p>
        </div>

        <div class="p-6 sm:p-7 space-y-5">

            @if($outcome !== null && $attendee !== null)
                <!-- CHECK-IN CONFIRMED RESULT -->
                <div class="text-center space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full {{ $outcome['already_checked_in'] ? 'bg-blue-50 text-[#02338d]' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $outcome['already_checked_in'] ? 'Already Checked In' : 'Attendance Verified ✓' }}
                        </span>
                        <h2 class="text-xl font-extrabold text-slate-900 mt-2">
                            Welcome, {{ $attendee->name }}
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Staff ID: <strong class="text-[#02338d]">{{ $attendee->staff_id }}</strong> · {{ $attendee->department }}
                        </p>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2 text-xs">
                        @if(! $outcome['already_checked_in'])
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 font-semibold">Check-In Points Earned</span>
                                <span class="font-extrabold text-emerald-600">+{{ $outcome['points_earned'] }} pts</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">Updated Points Balance</span>
                            <span class="font-extrabold text-[#02338d]">{{ number_format((int) $attendee->points_balance) }} pts</span>
                        </div>
                        @if($outcome['is_late'])
                            <p class="text-[11px] font-bold text-amber-700 pt-2 border-t border-slate-200">
                                Checked in during the 10-minute late grace window ({{ $outcome['minutes_late'] }} min late; -{{ $outcome['points_reduced_by'] }} pts applied).
                            </p>
                        @endif
                    </div>

                    @if($outcome['needs_onboarding'])
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3.5 text-xs text-amber-900 font-semibold">
                            You were auto-registered as a walk-in attendee. Log in to WellLoop to complete your Pre-Event WHO-5 baseline, waiver, and Post-Event feedback.
                        </div>
                    @endif

                    <a href="{{ route('login') }}"
                       class="w-full block text-center bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                        Log In to WellLoop Portal →
                    </a>
                </div>

            @else
                <!-- ERROR MESSAGE -->
                @if($scanError || $errors->any())
                    <div class="bg-rose-50 text-rose-700 px-4 py-3.5 rounded-2xl text-xs font-extrabold border border-rose-200">
                        {{ $scanError ?? $errors->first() }}
                    </div>
                @endif

                @if($isGateOpen)
                    <div class="rounded-2xl p-4 text-center border {{ $isLateGrace ? 'bg-amber-50 border-amber-200' : 'bg-emerald-50 border-emerald-200' }}">
                        <p class="text-xs font-extrabold {{ $isLateGrace ? 'text-amber-900' : 'text-emerald-900' }}">
                            {{ $isLateGrace ? 'Late Grace Window Active (Closes ' . $gateClosesAt->format('H:i') . ' EAT)' : 'Venue Check-In Gate is Open!' }}
                        </p>
                        <p class="text-[11px] {{ $isLateGrace ? 'text-amber-700' : 'text-emerald-700' }} mt-0.5">
                            Enter your Strathmore University Staff ID below to record your attendance immediately.
                        </p>
                    </div>

                    <form method="POST" action="{{ $verifyUrl }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                                Strathmore Staff ID <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   name="staff_id"
                                   value="{{ old('staff_id') }}"
                                   required
                                   autofocus
                                   placeholder="e.g., 000004"
                                   class="w-full border border-slate-300 rounded-xl text-sm font-extrabold text-slate-900 p-3.5 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d] tracking-wider">
                        </div>

                        <button type="submit"
                                class="w-full bg-[#02338d] hover:bg-[#01225e] text-white text-xs sm:text-sm font-extrabold py-3.5 rounded-xl shadow-sm transition">
                            Verify Staff ID & Check In →
                        </button>
                    </form>

                    <div class="pt-3 border-t border-slate-100 text-center">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-[#02338d] transition">
                            Already have an account? Log in to Staff Portal
                        </a>
                    </div>

                @elseif($isTooEarly)
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center space-y-1.5">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full">
                            Gate Not Yet Open
                        </span>
                        <p class="text-xs font-extrabold text-slate-800 pt-1">
                            Check-in opens at {{ $gateOpensAt->format('D, d M Y \a\t h:i A') }} EAT
                        </p>
                        <p class="text-[11px] text-slate-500">
                            Please scan again within 1 hour of the session start time.
                        </p>
                    </div>
                @else
                    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-center space-y-1.5">
                        <p class="text-xs font-extrabold text-rose-800">Event Check-In Gate Has Closed</p>
                        <p class="text-[11px] text-rose-600">
                            The 10-minute late grace window ended at {{ $gateClosesAt->format('h:i A') }} EAT. Please see the People & Culture desk for assistance.
                        </p>
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
@endsection