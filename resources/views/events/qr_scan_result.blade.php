@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto px-4 py-8">
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm text-center">

        <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
            {{ $autoRegistered ? 'Automatically Registered & Checked In' : ($alreadyCheckedIn ? 'Already Checked In' : 'Attendance Verified') }}
        </span>

        <h1 class="text-2xl font-extrabold text-slate-900 mt-3">{{ $event->name }}</h1>
        <p class="text-xs text-slate-500 mt-1">
            Verified at {{ optional($registration->checked_in_at)->format('H:i \o\n d M Y') }} • {{ auth()->user()->name }} ({{ auth()->user()->staff_id }})
        </p>

        @if($pointsEarned > 0)
            <div class="mt-6 bg-amber-50 border border-amber-200 text-amber-900 font-bold px-5 py-3.5 rounded-2xl text-xs flex justify-between items-center">
                <span>Check-in points credited:</span>
                <span class="bg-[#c99a2e] text-[#01225e] px-3 py-1 rounded-lg font-extrabold">+{{ $pointsEarned }} pts</span>
            </div>
        @endif

        @if($needsOnboarding)
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
        @else
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('feedback.create', $registration) }}"
                   class="flex-1 block text-center bg-[#02338d] hover:bg-[#01225e] text-white font-bold py-3.5 rounded-xl text-xs transition">
                    Open Post-Event Feedback
                </a>
                <a href="{{ route('events.index') }}"
                   class="flex-1 block text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl text-xs transition">
                    Done
                </a>
            </div>
        @endif

    </div>
</div>
@endsection