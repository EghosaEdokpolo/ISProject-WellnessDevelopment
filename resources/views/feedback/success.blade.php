@extends('layouts.app')

@section('content')
@php
    $event = $registration->event;
    $postPercent = round(($postScore / 25) * 100);
    $postOffset = 100 - $postPercent;
    $postColor = $postScore >= 19 ? '#10b981' : ($postScore >= 13 ? '#d97706' : '#ef4444');
    $postLabel = $postScore >= 19 ? 'Flourishing' : ($postScore >= 13 ? 'Moderate' : 'Reduced');
    $postBadgeClass = $postScore >= 19
        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
        : ($postScore >= 13 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-600 border-rose-200');
@endphp

<div class="max-w-xl mx-auto px-4 py-8">
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm text-center">
        
        <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-slate-900">Feedback Recorded!</h1>
        <p class="text-sm text-slate-500 mt-1 mb-8">
            Thank you for completing your post-event evaluation for <strong>{{ $event->name }}</strong>.
        </p>

        <!-- Single Post-Event Outcome Donut Gauge -->
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-8 flex flex-col items-center">
            <span class="text-[11px] font-extrabold text-[#02338d] uppercase tracking-widest mb-4">
                Post-Event WHO-5 Wellbeing Score
            </span>

            <div class="relative w-32 h-32">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9155" fill="transparent" stroke="#e2e8f0" stroke-width="3"></circle>
                    <circle cx="18" cy="18" r="15.9155" fill="transparent" stroke="{{ $postColor }}" stroke-width="3" stroke-dasharray="100" stroke-dashoffset="{{ $postOffset }}" stroke-linecap="round"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-extrabold text-slate-900">{{ $postScore }}<span class="text-xs text-slate-400">/25</span></span>
                    <span class="text-[11px] font-bold text-slate-500">{{ $postPercent }}%</span>
                </div>
            </div>

            <span class="mt-4 text-xs font-extrabold px-3 py-1 rounded-full border {{ $postBadgeClass }}">
                {{ $postLabel }} Wellbeing
            </span>
        </div>

        <!-- Reward Banner -->
        <div class="bg-amber-50 border border-amber-200 text-amber-900 font-bold px-5 py-4 rounded-2xl text-sm flex justify-between items-center mb-8">
            <span>Wellness points rewarded:</span>
            <span class="bg-[#c99a2e] text-[#01225e] px-4 py-1.5 rounded-xl font-extrabold text-xs shrink-0">+{{ $pointsEarned }} pts</span>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <a href="{{ route('events.index') }}" class="flex-1 block text-center bg-[#02338d] hover:bg-[#01225e] text-white font-bold py-3.5 rounded-xl text-xs transition">
                Return to Events
            </a>
            <a href="{{ route('rewards.index') }}" class="flex-1 block text-center bg-white border border-[#02338d]/20 text-[#02338d] hover:bg-slate-50 font-bold py-3.5 rounded-xl text-xs transition">
                View My Rewards →
            </a>
        </div>

    </div>
</div>
@endsection