@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap">
<style>
    .su-brand-scope, .su-brand-scope * {
        font-family: 'Montserrat', sans-serif;
    }
</style>

<div class="su-brand-scope max-w-7xl mx-auto space-y-8">
    <x-hr-nav />

    <div class="max-w-5xl mx-auto space-y-6">
        @if(session('success'))
            <div class="bg-[#02338d]/5 text-[#02338d] px-5 py-4 rounded-2xl text-xs font-bold border border-[#02338d]/20 shadow-sm">
                ✓ {{ session('success') }}
            </div>
        @endif

        <!-- Platform Configuration & Gate Policy -->
        <div class="bg-white rounded-3xl border border-[#1d1d1b]/10 p-6 sm:p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-5 border-b border-[#1d1d1b]/10">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#c99a2e]">Governance & Compliance</span>
                    <h1 class="text-lg font-extrabold text-[#1d1d1b] mt-0.5 tracking-tight">WellLoop Gate & Platform Rules</h1>
                    <p class="text-xs text-[#1d1d1b]/60 mt-0.5">Active institutional parameters for Strathmore University staff wellness sessions.</p>
                </div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider bg-[#02338d]/10 text-[#02338d] border border-[#02338d]/15 px-3 py-1.5 rounded-full">
                    {{ config('wellloop.timezone', 'Africa/Nairobi') }} (EAT)
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
                <div class="bg-[#1d1d1b]/[0.02] rounded-2xl p-5 border border-[#1d1d1b]/10">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#1d1d1b]/50">Gate Unlock Window</span>
                    <p class="text-2xl font-extrabold text-[#02338d] mt-1.5">{{ config('wellloop.checkin.opens_minutes_before', 60) }} Minutes</p>
                    <p class="text-[11px] text-[#1d1d1b]/60 mt-1">Before scheduled session start</p>
                </div>

                <div class="bg-[#f5eccd]/40 rounded-2xl p-5 border border-[#c99a2e]/30">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#8a6514]">Late Grace Allowance</span>
                    <p class="text-2xl font-extrabold text-[#8a6514] mt-1.5">{{ config('wellloop.checkin.late_grace_minutes', 10) }} Minutes</p>
                    <p class="text-[11px] text-[#1d1d1b]/65 mt-1">After start (-{{ config('wellloop.checkin.late_penalty_percent', 40) }}% points)</p>
                </div>

                <div class="bg-[#1d1d1b]/[0.02] rounded-2xl p-5 border border-[#1d1d1b]/10">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#1d1d1b]/50">Walk-In QR Check-In</span>
                    <p class="text-2xl font-extrabold {{ config('wellloop.checkin.allow_walk_in_qr', true) ? 'text-[#02338d]' : 'text-[#e03c31]' }} mt-1.5">
                        {{ config('wellloop.checkin.allow_walk_in_qr', true) ? 'Enabled' : 'Disabled' }}
                    </p>
                    <p class="text-[11px] text-[#1d1d1b]/60 mt-1">Auto-registers staff at venue door</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection