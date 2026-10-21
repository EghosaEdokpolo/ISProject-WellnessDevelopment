@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Wellness Content Hub</h1>
        <p class="text-sm text-gray-500 mt-1">UC-17 · Guides and resources to support you between events.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-3xl mb-4">🧘</div>
            <span class="inline-block bg-wl-lavender text-wl-indigo text-[10px] font-bold px-2 py-1 rounded-md mb-3">Physical</span>
            <h3 class="font-semibold text-gray-900 text-sm">5-min desk stretch guide</h3>
        </div>
        <!-- Card 2 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-3xl mb-4">🧠</div>
            <span class="inline-block bg-wl-mint text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md mb-3">Mental</span>
            <h3 class="font-semibold text-gray-900 text-sm">Managing exam-season stress</h3>
        </div>
        <!-- Card 3 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-3xl mb-4">🥗</div>
            <span class="inline-block bg-wl-lavender text-wl-indigo text-[10px] font-bold px-2 py-1 rounded-md mb-3">Physical</span>
            <h3 class="font-semibold text-gray-900 text-sm">Nutrition on a campus budget</h3>
        </div>
        <!-- Card 4 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-3xl mb-4">🌙</div>
            <span class="inline-block bg-wl-mint text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-md mb-3">Mental</span>
            <h3 class="font-semibold text-gray-900 text-sm">Sleep hygiene for shift workers</h3>
        </div>
        <!-- Card 5 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-3xl mb-4">🤝</div>
            <span class="inline-block bg-wl-lilac text-purple-700 text-[10px] font-bold px-2 py-1 rounded-md mb-3">Social</span>
            <h3 class="font-semibold text-gray-900 text-sm">Peer support: when to seek help</h3>
        </div>
        <!-- Card 6 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-3xl mb-4">💰</div>
            <span class="inline-block bg-wl-cream text-amber-700 text-[10px] font-bold px-2 py-1 rounded-md border border-amber-200 mb-3">Financial</span>
            <h3 class="font-semibold text-gray-900 text-sm">Financial wellbeing basics</h3>
        </div>
    </div>
@endsection