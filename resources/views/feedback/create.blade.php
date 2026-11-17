@extends('layouts.app')

@push('styles')
<style>
    .who5-radio { display: none; }
    .who5-radio:checked + .who5-label {
        background-color: #02338d;
        color: #ffffff;
        border-color: #02338d;
        box-shadow: 0 4px 10px rgba(2, 51, 141, 0.2);
    }
    .scale10-radio { display: none; }
    .scale10-radio:checked + .scale10-label {
        background-color: #02338d;
        color: #ffffff;
        border-color: #02338d;
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <div class="mb-8">
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Post-Event Feedback</h1>
            <span class="text-[10px] font-bold text-[#02338d] bg-[#e8f0fe] px-2.5 py-1 rounded-full uppercase tracking-wider">Lagging Impact</span>
        </div>
        <p class="text-sm text-slate-500 mt-1">
            {{ $registration->event->name ?? 'Wellness Session' }} • WHO-5 Wellbeing Index (0–25 scale; scores below 13 indicate low wellbeing).
        </p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-rose-50 text-rose-600 px-4 py-3 rounded-xl text-sm font-bold border border-rose-200">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-rose-50 text-rose-600 px-4 py-3 rounded-xl text-sm font-bold border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <!-- Live Post-Event WHO-5 Donut Chart Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="relative w-28 h-28 shrink-0">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9155" fill="transparent" stroke="#f1f5f9" stroke-width="3"></circle>
                        <circle id="post-gauge-stroke"
                                cx="18"
                                cy="18"
                                r="15.9155"
                                fill="transparent"
                                stroke="#02338d"
                                stroke-width="3"
                                stroke-dasharray="100"
                                stroke-dashoffset="100"
                                stroke-linecap="round"
                                class="transition-all duration-500"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-extrabold text-slate-900">
                            <span id="live-score">0</span><span class="text-xs font-normal text-slate-400">/25</span>
                        </span>
                        <span id="post-gauge-percent" class="text-[10px] font-bold text-slate-500">0%</span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base font-extrabold text-slate-900">Live Post-Event WHO-5 Score</h2>
                        <span id="post-gauge-status" class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500">
                            Pending
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1.5 max-w-md" id="who5-interpretation">
                        Select your responses below to see your live post-event wellbeing score update in real time.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 text-[10px] sm:text-xs font-bold text-center border-t border-slate-100 mt-6 pt-4 text-slate-500">
            <div class="flex items-center justify-center gap-1.5">
                <span class="w-2.5 h-2.5 bg-red-500 rounded-full inline-block shrink-0"></span>
                <span>0–12 Reduced</span>
            </div>
            <div class="flex items-center justify-center gap-1.5">
                <span class="w-2.5 h-2.5 bg-amber-600 rounded-full inline-block shrink-0"></span>
                <span>13–18 Moderate</span>
            </div>
            <div class="flex items-center justify-center gap-1.5">
                <span class="w-2.5 h-2.5 bg-emerald-600 rounded-full inline-block shrink-0"></span>
                <span>19–25 Flourishing</span>
            </div>
        </div>
    </div>

    <!-- Post-Event Feedback Form -->
    <form method="POST"
          action="{{ route('feedback.store', $registration) }}"
          onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.classList.add('opacity-70', 'cursor-not-allowed');"
          class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <div class="p-6 sm:p-8">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-4">
                <h2 class="text-lg font-bold text-slate-900">Post-Event WHO-5 Wellbeing Assessment</h2>
                <span class="text-xs font-bold text-[#02338d]">0 to 5 Scale</span>
            </div>

            <p class="text-sm text-slate-500 mb-6">Please indicate for each of the five statements how you have felt following the wellness session:</p>

            @php
                $questions = [
                    'item_cheerful'   => '1. I have felt cheerful and in good spirits',
                    'item_calm'       => '2. I have felt calm and relaxed',
                    'item_active'     => '3. I have felt active and vigorous',
                    'item_rested'     => '4. I woke up feeling fresh and rested',
                    'item_interested' => '5. My daily life has been filled with things that interest me',
                ];
                $options = [
                    0 => ['title' => '0', 'label' => 'At no time'],
                    1 => ['title' => '1', 'label' => 'Some of the time'],
                    2 => ['title' => '2', 'label' => 'Less than half the time'],
                    3 => ['title' => '3', 'label' => 'More than half the time'],
                    4 => ['title' => '4', 'label' => 'Most of the time'],
                    5 => ['title' => '5', 'label' => 'All of the time'],
                ];
            @endphp

            <div class="space-y-8 mb-8">
                @foreach($questions as $field => $text)
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-3">{{ $text }}</label>
                        <div class="grid grid-cols-2 sm:grid-cols-6 gap-2">
                            @foreach($options as $value => $opt)
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="{{ $field }}" value="{{ $value }}" class="who5-radio"
                                           {{ (string) old($field) === (string) $value ? 'checked' : '' }} required>
                                    <div class="who5-label border border-slate-200 rounded-xl py-3 px-1.5 text-slate-600 hover:bg-slate-50 transition-all h-full flex flex-col justify-center">
                                        <div class="font-extrabold text-base">{{ $opt['title'] }}</div>
                                        <div class="text-[10px] leading-tight mt-1 font-semibold">{{ $opt['label'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <hr class="border-slate-100 mb-8">

            <!-- Overall Satisfaction & 1-10 Social Scale -->
            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">2. Overall Session Satisfaction (1–5 Stars)</label>
                    <p class="text-xs text-slate-500 mb-3">1 = Very Dissatisfied • 3 = Satisfactory • 5 = Exceptional</p>
                    <div class="flex gap-2 text-3xl cursor-pointer" id="star-container">
                        <span onclick="setStars(1)" class="text-slate-300 transition">★</span>
                        <span onclick="setStars(2)" class="text-slate-300 transition">★</span>
                        <span onclick="setStars(3)" class="text-slate-300 transition">★</span>
                        <span onclick="setStars(4)" class="text-slate-300 transition">★</span>
                        <span onclick="setStars(5)" class="text-slate-300 transition">★</span>
                    </div>
                    <input type="hidden" name="satisfaction_stars" id="rating-input" value="{{ old('satisfaction_stars', 5) }}" required>
                </div>

                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <div>
                            <label class="block text-sm font-bold text-slate-800">3. Social Connection & Session Engagement Quality (1–10 Scale)</label>
                            <p class="text-xs text-slate-500 mt-0.5">Rate how connected, supported, and engaged you felt with colleagues during this session.</p>
                        </div>
                        <span class="text-xs font-extrabold text-[#02338d] bg-[#e8f0fe] px-3 py-1.5 rounded-full w-fit" id="scale10-badge">
                            7 — Good: Warm peer interaction and active participation
                        </span>
                    </div>

                    @php
                        $scaleDescriptions = [
                            1  => '1 — Isolated: Felt disconnected; no meaningful interaction',
                            2  => '2 — Very Low: Minimal participation or peer connection',
                            3  => '3 — Low: Passive attendance; limited engagement',
                            4  => '4 — Below Average: Slightly awkward or uninvolved',
                            5  => '5 — Neutral: Average session; polite but surface-level',
                            6  => '6 — Moderate: Comfortable atmosphere; some interaction',
                            7  => '7 — Good: Warm peer interaction and active participation',
                            8  => '8 — Very Good: Strong camaraderie and supportive group energy',
                            9  => '9 — Excellent: Deeply connected, energized, and heard',
                            10 => '10 — Transformative: Exceptional team bonding & belonging',
                        ];
                    @endphp

                    <div class="grid grid-cols-5 sm:grid-cols-10 gap-2 mb-3">
                        @foreach($scaleDescriptions as $num => $desc)
                            <label class="cursor-pointer text-center">
                                <input type="radio" name="social_quality" value="{{ $num }}" class="scale10-radio"
                                       data-desc="{{ $desc }}"
                                       {{ (int) old('social_quality', 7) === $num ? 'checked' : '' }} required>
                                <div class="scale10-label border border-slate-200 rounded-xl py-2.5 font-extrabold text-sm text-slate-700 hover:bg-slate-50 transition">
                                    {{ $num }}
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px] bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-600">
                        <div><strong class="text-rose-600">1–3 (Low):</strong> Disconnected, passive, or minimal peer interaction</div>
                        <div><strong class="text-amber-600">4–7 (Moderate–Good):</strong> Comfortable participation & constructive discussion</div>
                        <div><strong class="text-emerald-700">8–10 (High):</strong> Strong team bonding, belonging & energizing support</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">4. Qualitative Feedback & Key Takeaways</label>
                    <textarea name="comments" rows="3" placeholder="Share what worked well, how this session impacted your wellbeing, or suggestions for People & Culture..."
                              class="w-full border border-slate-300 rounded-xl text-sm p-4 bg-slate-50 focus:ring-[#02338d] focus:border-[#02338d]">{{ old('comments') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 px-8 py-4 border-t border-slate-200 flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400">Reward:</span>
                <span class="text-xs font-bold bg-[#c99a2e]/20 text-amber-900 px-2.5 py-1 rounded-md">
                    +{{ $registration->event->points_feedback ?? 10 }} Wellness Points
                </span>
            </div>
            <button type="submit" class="bg-[#02338d] text-white font-bold py-3 px-8 rounded-xl shadow-sm hover:bg-[#01225e] transition flex items-center gap-2">
                Submit Post-Event Feedback
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const who5Fields = ['item_cheerful', 'item_calm', 'item_active', 'item_rested', 'item_interested'];
        const radios = document.querySelectorAll('.who5-radio');
        const scoreDisplay = document.getElementById('live-score');
        const postStroke = document.getElementById('post-gauge-stroke');
        const postPercentText = document.getElementById('post-gauge-percent');
        const postStatusText = document.getElementById('post-gauge-status');
        const interpDisplay = document.getElementById('who5-interpretation');

        function updateWho5() {
            let total = 0;
            let answered = 0;

            who5Fields.forEach(name => {
                const selected = document.querySelector(`input[name="${name}"]:checked`);
                if (selected) {
                    total += parseInt(selected.value, 10);
                    answered++;
                }
            });

            const percent = Math.round((total / 25) * 100);
            scoreDisplay.innerText = total;
            postPercentText.innerText = percent + '%';
            postStroke.style.strokeDashoffset = 100 - percent;

            if (answered === 0) {
                postStatusText.innerText = 'Pending';
                postStatusText.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500';
                postStroke.setAttribute('stroke', '#02338d');
                return;
            }

            if (total < 13) {
                postStatusText.innerText = 'Reduced';
                postStatusText.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-600';
                postStroke.setAttribute('stroke', '#ef4444');
                if (answered === 5) {
                    interpDisplay.innerText = 'Score below 13: Indicates reduced wellbeing — People & Culture support resources are available.';
                    interpDisplay.className = 'text-xs font-bold text-rose-600 mt-1.5 max-w-md';
                }
            } else if (total <= 18) {
                postStatusText.innerText = 'Moderate';
                postStatusText.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700';
                postStroke.setAttribute('stroke', '#d97706');
                if (answered === 5) {
                    interpDisplay.innerText = 'Score 13–18: Moderate wellbeing — continue engaging in regular wellness activities.';
                    interpDisplay.className = 'text-xs font-bold text-amber-700 mt-1.5 max-w-md';
                }
            } else {
                postStatusText.innerText = 'Flourishing';
                postStatusText.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700';
                postStroke.setAttribute('stroke', '#10b981');
                if (answered === 5) {
                    interpDisplay.innerText = 'Score 19–25: Strong positive wellbeing and flourishing energy.';
                    interpDisplay.className = 'text-xs font-bold text-emerald-700 mt-1.5 max-w-md';
                }
            }

            if (answered < 5) {
                interpDisplay.innerText = `${answered} of 5 items answered — complete the remaining questions to finalize your score.`;
                interpDisplay.className = 'text-xs text-slate-500 mt-1.5 max-w-md';
            }
        }

        radios.forEach(r => r.addEventListener('change', updateWho5));
        updateWho5();

        const scaleRadios = document.querySelectorAll('.scale10-radio');
        const scaleBadge = document.getElementById('scale10-badge');
        scaleRadios.forEach(r => {
            if (r.checked) {
                scaleBadge.innerText = r.dataset.desc;
            }
            r.addEventListener('change', function () {
                if (this.checked) {
                    scaleBadge.innerText = this.dataset.desc;
                }
            });
        });

        setStars(parseInt(document.getElementById('rating-input').value || '5', 10));
    });

    function setStars(count) {
        const stars = document.querySelectorAll('#star-container span');
        const input = document.getElementById('rating-input');
        stars.forEach((star, index) => {
            if (index < count) {
                star.classList.add('text-[#c99a2e]');
                star.classList.remove('text-slate-300');
            } else {
                star.classList.remove('text-[#c99a2e]');
                star.classList.add('text-slate-300');
            }
        });
        input.value = count;
    }
</script>
@endpush