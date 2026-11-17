@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">

    <!-- Stepper Header -->
    <div class="mb-8 text-center">
        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-wl-indigo hover:underline inline-flex items-center justify-center gap-1 mb-6">&larr; Back to events</a>
        <div class="flex justify-center items-center gap-2 md:gap-4 text-xs font-semibold text-gray-400 mb-8">
            <span class="flex flex-col items-center"><span class="w-8 h-8 rounded-full bg-wl-indigo text-white flex items-center justify-center mb-1">✓</span> Details</span>
            <span class="w-8 md:w-12 h-px bg-wl-indigo"></span>
            <span class="flex flex-col items-center"><span class="w-8 h-8 rounded-full bg-wl-gold text-white flex items-center justify-center mb-1 shadow-[0_0_10px_rgba(201,154,46,0.5)]">2</span> Baseline</span>
            <span class="w-8 md:w-12 h-px bg-gray-300"></span>
            <span class="flex flex-col items-center"><span class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mb-1">3</span> Waiver</span>
            <span class="w-8 md:w-12 h-px bg-gray-300"></span>
            <span class="flex flex-col items-center"><span class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mb-1">4</span> Consent</span>
            <span class="w-8 md:w-12 h-px bg-gray-300"></span>
            <span class="flex flex-col items-center"><span class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mb-1">5</span> Ticket</span>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-sm font-bold border border-rose-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-sm font-bold border border-rose-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('registrations.who5.store', $registration) }}" id="who5Form" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.classList.add('opacity-70', 'cursor-not-allowed');">
        @csrf
        
        <div>
            <!-- Event Info Banner -->
            <div class="bg-wl-lavender/40 rounded-xl p-4 mb-8 flex items-center gap-3 border border-wl-indigo/10">
                <span class="bg-wl-indigo text-white text-[10px] font-bold px-2.5 py-1 rounded uppercase tracking-wider">{{ $registration->event->category }}</span>
                <div>
                    <h3 class="font-bold text-gray-900">{{ $registration->event->name }}</h3>
                    <p class="text-xs text-gray-600">{{ $registration->event->starts_at->format('D, d M Y') }} · {{ $registration->event->location }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- LEFT COLUMN: The Survey -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 md:p-8">
                        <div class="mb-8 border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-xl font-bold text-slate-900">Pre-event Wellbeing Baseline</h2>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-full uppercase tracking-wider">Leading</span>
                            </div>
                            <p class="text-sm text-slate-500">Rate each statement based on how you have felt over the <strong>past two weeks</strong>.</p>
                        </div>

                        @php
                            $questions = [
                                'item_cheerful'   => 'I have felt cheerful and in good spirits',
                                'item_calm'       => 'I have felt calm and relaxed',
                                'item_active'     => 'I have felt active and vigorous',
                                'item_rested'     => 'I woke up feeling fresh and rested',
                                'item_interested' => 'My daily life has been filled with things that interest me',
                            ];
                            // 0 to 5 scale (6 options) for accurate 0-25 WHO-5 scoring
                            $scale = [
                                0 => 'At no time',
                                1 => 'Some of the time',
                                2 => 'Less than half',
                                3 => 'More than half',
                                4 => 'Most of the time',
                                5 => 'All of the time',
                            ];
                        @endphp

                        <div class="space-y-8">
                            @foreach($questions as $index => $label)
                                <div>
                                    <label class="block text-sm font-bold text-slate-800 mb-3">{{ $loop->iteration }}. {{ $label }}</label>
                                    <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                                        @foreach($scale as $value => $scaleLabel)
                                            <label class="cursor-pointer relative">
                                                <input type="radio" name="{{ $index }}" value="{{ $value }}" class="peer sr-only"
                                                       {{ (string) old($index) === (string) $value ? 'checked' : '' }} required>
                                                <div class="h-full text-center border-2 border-slate-200 rounded-xl py-3 px-1 hover:bg-slate-50 peer-checked:bg-brand peer-checked:border-brand peer-checked:text-white peer-checked:shadow-md transition-all duration-200 flex flex-col items-center justify-center">
                                                    <span class="block font-black text-lg mb-0.5">{{ $value }}</span>
                                                    <span class="text-[9px] leading-tight opacity-75 font-medium">{{ $scaleLabel }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error($index)
                                        <p class="text-xs font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Sticky Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6 md:p-8 sticky top-24">
                        <h3 class="text-lg font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4 text-center">Your Baseline</h3>
                        
                        <div class="text-center mb-4">
                            <div class="relative inline-flex items-center justify-center">
                                <!-- SVG Progress Ring -->
                                <svg class="w-40 h-40 transform -rotate-90">
                                    <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-100" />
                                    <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" 
                                            stroke-dasharray="439.8" stroke-dashoffset="439.8" 
                                            class="text-brand transition-all duration-500 ease-out" id="score-ring" />
                                </svg>
                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="text-5xl font-black text-slate-900" id="live-score">0</span>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">out of 25</span>
                                </div>
                            </div>
                        </div>

                        <p id="baseline-status" class="text-xs font-bold text-center text-slate-500 mb-5">
                            Answer all 5 statements to calculate your baseline.
                        </p>

                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-sm text-slate-500 mb-6 text-center">
                            This score is your <strong>baseline</strong>. After the event, you will take the survey again to measure your improvement.
                        </div>

                        <button type="submit" class="w-full bg-brand text-white font-black text-lg py-4 rounded-xl shadow-lg hover:bg-blue-800 hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                            Continue to Waiver 
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<!-- JavaScript for Live Score Calculation -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('who5Form');
        const scoreDisplay = document.getElementById('live-score');
        const scoreRing = document.getElementById('score-ring');
        const statusText = document.getElementById('baseline-status');
        
        // Max score is 25 (5 questions × 5 points)
        const maxScore = 25; 
        const circumference = 2 * Math.PI * 70; // 439.82

        function calculateScore() {
            let total = 0;
            let answered = 0;
            const questions = ['item_cheerful', 'item_calm', 'item_active', 'item_rested', 'item_interested'];
            
            questions.forEach(q => {
                const selected = document.querySelector(`input[name="${q}"]:checked`);
                if (selected) {
                    total += parseInt(selected.value, 10);
                    answered++;
                }
            });

            // Update number
            scoreDisplay.textContent = total;

            // Update SVG ring
            const percentage = total / maxScore;
            const offset = circumference - (percentage * circumference);
            scoreRing.style.strokeDashoffset = offset;

            if (answered < 5) {
                scoreRing.classList.remove('text-rose-500', 'text-emerald-500');
                scoreRing.classList.add('text-brand');
                statusText.textContent = `${answered} of 5 statements answered`;
                statusText.className = 'text-xs font-bold text-center text-slate-500 mb-5';
            } else if (total < 13) {
                scoreRing.classList.remove('text-brand', 'text-emerald-500');
                scoreRing.classList.add('text-rose-500');
                statusText.textContent = 'Score < 13: Reduced baseline wellbeing';
                statusText.className = 'text-xs font-bold text-center text-rose-600 mb-5';
            } else {
                scoreRing.classList.remove('text-brand', 'text-rose-500');
                scoreRing.classList.add('text-emerald-500');
                statusText.textContent = 'Score ≥ 13: Healthy baseline wellbeing';
                statusText.className = 'text-xs font-bold text-center text-emerald-600 mb-5';
            }
        }

        // Listen for changes on all radio buttons & initialize on load
        form.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', calculateScore);
        });
        calculateScore();
    });
</script>
@endsection