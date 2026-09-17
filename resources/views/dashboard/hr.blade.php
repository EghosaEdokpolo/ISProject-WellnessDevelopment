@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">HR Overview Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">WPMIS — Wellness Programme Management &amp; Impact System</p>

    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Leading Indicators — System Adoption</p>
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex justify-between items-center">
                <span class="text-xs font-semibold text-gray-500 uppercase">Registration Rate</span>
                <span class="text-[10px] bg-wl-mint text-emerald-700 rounded-full px-2 py-0.5">Leading</span>
            </div>
            <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $registrationRate }}%</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <span class="text-xs font-semibold text-gray-500 uppercase">Events this semester</span>
            <p class="text-3xl font-bold text-wl-indigo mt-2">{{ $eventsThisSemester }}</p>
        </div>
    </div>

    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Lagging Indicators — Wellness Outcomes</p>
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="flex justify-between items-center">
                <span class="text-xs font-semibold text-gray-500 uppercase">Wellness Impact Score</span>
                <span class="text-[10px] bg-red-50 text-red-600 rounded-full px-2 py-0.5">Lagging</span>
            </div>
            {{-- POINTER: $avgLift is calculated in DashboardController by
                 comparing average 'post' scores against average 'pre'
                 scores in the wellbeing_responses table. --}}
            <p class="text-3xl font-bold text-emerald-600 mt-2">+{{ $avgLift }}</p>
            <p class="text-xs text-gray-400">avg WHO-5 pre→post delta</p>
        </div>
    </div>
@endsection
