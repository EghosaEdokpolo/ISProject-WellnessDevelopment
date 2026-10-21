@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Post-Event Feedback</h1>
        <p class="text-sm text-gray-500 mt-1">UC-08 · WHO-5 Post-event measurement</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-2xl mx-auto">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-1">Feedback submitted — thank you!</h2>
                <p class="text-sm text-gray-500 mb-4">A long-term follow-up will be sent in 4–6 weeks (UC-09).</p>
                <div class="flex gap-2">
                    <span class="bg-wl-lavender text-wl-indigo text-xs font-bold px-3 py-1 rounded-full border border-wl-indigo/20">+20 check-in</span>
                    <span class="bg-wl-cream text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200">+15 feedback</span>
                </div>
            </div>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-100">
            <a href="{{ route('rewards.index') }}" class="w-full block bg-wl-navy text-white text-center font-bold py-3 rounded-lg hover:bg-gray-800 transition-colors">
                View Leaderboard
            </a>
        </div>
    </div>

    <!-- CONFETTI SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#5b5fc7', '#c99a2e', '#dcf3e4']
            });
        });
    </script>
@endsection