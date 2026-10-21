@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Post-Event Feedback</h1>
            <p class="text-sm text-gray-500 mt-1">UC-08 · WHO-5 Wellbeing Index — validated instrument (score 0–25). Score below 13 indicates poor wellbeing.</p>
        </div>
        <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider mb-4">Lagging</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-gray-900">Post-event Wellbeing</h2>
            <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded-md uppercase">Lagging</span>
        </div>
        
        <p class="text-sm text-gray-500 mb-6">Rate each statement for the past two weeks.</p>

        <form method="POST" action="{{ route('feedback.store', $registration) }}">
            @csrf

            @php
                $questions = [
                    'item_cheerful' => 'I have felt cheerful and in good spirits',
                    'item_calm' => 'I have felt calm and relaxed',
                    'item_active' => 'I have felt active and vigorous',
                    'item_rested' => 'I woke up feeling fresh and rested',
                    'item_interested' => 'My daily life has been filled with things that interest me',
                ];
                $scale = ['At no time', 'Some of the time', 'Less than half', 'More than half', 'All of the time'];
            @endphp

            <div class="space-y-6 mb-8">
                @foreach($questions as $index => $label)
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-3">{{ $loop->iteration }}. {{ $label }}</label>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($scale as $value => $scaleLabel)
                                <label class="cursor-pointer">
                                    <input type="radio" name="{{ $index }}" value="{{ $value }}" class="peer sr-only" required>
                                    <div class="text-center border border-gray-200 rounded-lg py-2 hover:bg-gray-50 peer-checked:bg-wl-indigo peer-checked:border-wl-indigo peer-checked:text-white transition-colors">
                                        <div class="font-bold text-sm">{{ $value }}</div>
                                        <div class="text-[9px] leading-tight opacity-70">{{ $scaleLabel }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-wl-lavender/50 rounded-lg p-4 flex justify-between items-center mb-8 border border-wl-indigo/10">
                <span class="text-sm font-semibold text-wl-indigo">WHO-5 Score</span>
                <div class="text-2xl font-extrabold text-emerald-600"><span id="who5-total">0</span><span class="text-sm text-gray-400 font-normal">/25</span></div>
            </div>

            <hr class="border-gray-100 mb-6">

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-800 mb-2">Overall satisfaction</label>
                <div class="flex gap-1 text-2xl text-wl-gold cursor-pointer">
                    ★★★★☆
                </div>
            </div>

            <!-- DESCRIPTIVE SLIDER FOR STAKEHOLDER -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-semibold text-gray-800">Social interaction quality</label>
                    <span class="text-sm font-bold text-wl-indigo" id="social-val">7<span class="text-xs text-gray-400 font-normal">/10</span></span>
                </div>
                <input type="range" name="social_quality" min="1" max="10" value="7" class="w-full accent-wl-indigo" oninput="document.getElementById('social-val').innerHTML = this.value + '<span class=\'text-xs text-gray-400 font-normal\'>/10</span>'">
                <div class="flex justify-between text-[10px] text-gray-400 mt-1 px-1">
                    <span>1 = Poor (No interaction)</span>
                    <span>5 = Average</span>
                    <span>10 = Excellent (Deep connection)</span>
                </div>
            </div>

            <div class="mb-8">
                <textarea name="comments" rows="2" placeholder="Any additional comments..." class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:ring-wl-indigo focus:border-wl-indigo bg-gray-50"></textarea>
            </div>

            <div class="flex justify-between items-center mb-6">
                <span class="text-xs text-gray-500">Points preview</span>
                <span class="text-xs font-bold text-wl-indigo">+35 pts (check-in +20, feedback +15)</span>
            </div>

            <button type="submit" class="w-full bg-wl-gold text-wl-navy font-bold py-3 rounded-lg shadow-sm hover:bg-yellow-600 transition-colors">
                Submit feedback
            </button>
        </form>
    </div>

    <script>
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', () => {
                let total = 0;
                ['item_cheerful', 'item_calm', 'item_active', 'item_rested', 'item_interested'].forEach(item => {
                    const selected = document.querySelector(`input[name="${item}"]:checked`);
                    if (selected) total += parseInt(selected.value);
                });
                document.getElementById('who5-total').textContent = total;
            });
        });
    </script>
@endsection