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
<div class="max-w-7xl mx-auto px-4 py-4">

    <!-- Compact Top Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Post-Event Feedback</h1>
                <span class="text-[10px] font-extrabold text-[#02338d] bg-[#e8f0fe] px-2.5 py-1 rounded-full uppercase tracking-wider">
                    Lagging Impact
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                {{ $registration->event->name ?? 'Wellness Session' }} • WHO-5 Wellbeing Index (0–25 scale; scores below 13 indicate low wellbeing).
            </p>
        </div>

        <a href="{{ route('feedback.index') }}" class="text-xs font-extrabold text-slate-500 hover:text-[#02338d] transition">
            ← Back to Wellness Journey
        </a>
    </div>

    <!-- Server-Side Validation Warning Banner -->
    @if($errors->any())
        <div class="mb-6 bg-amber-50 text-amber-950 px-5 py-4 rounded-2xl text-xs font-extrabold border-2 border-amber-400 shadow-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-[#e03c31] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
            </svg>
            <div>
                <span class="block text-[#e03c31] uppercase tracking-wider text-[11px]">Incomplete Submission Warning</span>
                <ul class="list-disc list-inside mt-1 space-y-0.5 font-bold">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Client-Side Interactive Warning Banner (Triggered When Anything Is Unanswered) -->
    <div id="post-who5-warning-banner" class="hidden mb-6 bg-amber-50 text-amber-950 px-5 py-4 rounded-2xl text-xs font-extrabold border-2 border-[#e03c31] shadow-sm flex items-start gap-3">
        <svg class="w-5 h-5 text-[#e03c31] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
        </svg>
        <div>
            <span class="block text-[#e03c31] uppercase tracking-wider text-[11px]">Action Required Before Submitting</span>
            <p id="post-who5-warning-text" class="mt-0.5 font-bold text-slate-800"></p>
        </div>
    </div>

    <!-- Space-Efficient 12-Column Layout: Left Compact Graph (4 Cols) + Right Survey Form (8 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN: Compact Sticky Live Post-Event WHO-5 Donut Graph -->
        <div class="lg:col-span-4 lg:sticky lg:top-20 space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <h2 class="text-sm font-extrabold text-slate-900">Live Post-Event WHO-5</h2>
                    <span id="post-gauge-status" class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500">
                        Pending
                    </span>
                </div>

                <!-- Compact Donut & Score Row -->
                <div class="flex items-center gap-4">
                    <div class="relative w-24 h-24 shrink-0">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9155" fill="transparent" stroke="#f1f5f9" stroke-width="3.2"></circle>
                            <circle id="post-gauge-stroke"
                                    cx="18"
                                    cy="18"
                                    r="15.9155"
                                    fill="transparent"
                                    stroke="#02338d"
                                    stroke-width="3.2"
                                    stroke-dasharray="100"
                                    stroke-dashoffset="100"
                                    stroke-linecap="round"
                                    class="transition-all duration-500"></circle>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xl font-extrabold text-slate-900">
                                <span id="live-score">0</span><span class="text-[11px] font-normal text-slate-400">/25</span>
                            </span>
                            <span id="post-gauge-percent" class="text-[10px] font-bold text-slate-500">0%</span>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-slate-500 leading-snug" id="who5-interpretation">
                            Select all 5 responses on the right to calculate your live wellbeing score.
                        </p>
                        @if(isset($preWellbeing) && $preWellbeing)
                            <div class="mt-2 inline-flex items-center gap-1.5 bg-[#e8f0fe] text-[#02338d] px-2.5 py-1 rounded-lg text-[11px] font-extrabold">
                                <span>Pre-Event Baseline: {{ $preWellbeing->total_score }}/25</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Compact 3-Tier Scale Legend -->
                <div class="mt-4 pt-3.5 border-t border-slate-100 space-y-1.5 text-[11px] font-bold text-slate-600">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            <span>Reduced Wellbeing</span>
                        </span>
                        <span class="text-slate-400">0 – 12</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-amber-600 rounded-full"></span>
                            <span>Moderate Wellbeing</span>
                        </span>
                        <span class="text-slate-400">13 – 18</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-emerald-600 rounded-full"></span>
                            <span>Flourishing</span>
                        </span>
                        <span class="text-slate-400">19 – 25</span>
                    </div>
                </div>

                <!-- Live Completion Progress Pill -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-extrabold">
                    <span class="text-slate-400">Questions Answered</span>
                    <span id="post-answered-counter" class="text-[#02338d] bg-[#e8f0fe] px-2.5 py-0.5 rounded-full">0 / 5</span>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Post-Event WHO-5 & Session Feedback Form -->
        <div class="lg:col-span-8">
            <form method="POST"
                  id="post-feedback-form"
                  action="{{ route('feedback.store', $registration) }}"
                  novalidate
                  class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                @csrf

                <div class="p-6 sm:p-7">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                        <h2 class="text-base font-extrabold text-slate-900">1. Post-Event WHO-5 Wellbeing Assessment</h2>
                        <span class="text-[11px] font-extrabold text-[#02338d] bg-[#e8f0fe] px-2.5 py-0.5 rounded-full">0 to 5 Scale</span>
                    </div>

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
                            2 => ['title' => '2', 'label' => 'Less than half'],
                            3 => ['title' => '3', 'label' => 'More than half'],
                            4 => ['title' => '4', 'label' => 'Most of the time'],
                            5 => ['title' => '5', 'label' => 'All of the time'],
                        ];
                    @endphp

                    <div class="space-y-5 mb-7">
                        @foreach($questions as $field => $text)
                            <div id="question-block-{{ $field }}"
                                 class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/40 transition-all">
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <label class="block text-xs sm:text-sm font-extrabold text-slate-800">{{ $text }}</label>
                                    <span id="warn-{{ $field }}" class="hidden text-[11px] font-extrabold text-[#e03c31] bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full shrink-0">
                                        ⚠️ Required
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                                    @foreach($options as $value => $opt)
                                        <label class="cursor-pointer text-center">
                                            <input type="radio"
                                                   name="{{ $field }}"
                                                   value="{{ $value }}"
                                                   class="who5-radio"
                                                   {{ (string) old($field) === (string) $value ? 'checked' : '' }}
                                                   required>
                                            <div class="who5-label border border-slate-200 bg-white rounded-xl py-2.5 px-1 text-slate-600 hover:bg-slate-50 transition-all h-full flex flex-col justify-center">
                                                <div class="font-extrabold text-sm">{{ $opt['title'] }}</div>
                                                <div class="text-[10px] leading-tight mt-0.5 font-semibold">{{ $opt['label'] }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error($field)
                                    <p class="text-[11px] font-extrabold text-[#e03c31] mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <hr class="border-slate-100 mb-6">

                    <!-- Overall Satisfaction & 1-10 Social Scale -->
                    <div class="space-y-6">
                        <div id="block-satisfaction_stars" class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/40">
                            <div class="flex items-center justify-between gap-2">
                                <label class="block text-xs sm:text-sm font-extrabold text-slate-800">2. Overall Session Satisfaction (1–5 Stars)</label>
                                <span id="warn-satisfaction_stars" class="hidden text-[11px] font-extrabold text-[#e03c31]">⚠️ Please select a star rating</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5 mb-2">1 = Very Dissatisfied • 3 = Satisfactory • 5 = Exceptional</p>
                            <div class="flex gap-2 text-2xl cursor-pointer" id="star-container">
                                <span onclick="setStars(1)" class="text-slate-300 transition">★</span>
                                <span onclick="setStars(2)" class="text-slate-300 transition">★</span>
                                <span onclick="setStars(3)" class="text-slate-300 transition">★</span>
                                <span onclick="setStars(4)" class="text-slate-300 transition">★</span>
                                <span onclick="setStars(5)" class="text-slate-300 transition">★</span>
                            </div>
                            <input type="hidden" name="satisfaction_stars" id="rating-input" value="{{ old('satisfaction_stars', 5) }}" required>
                        </div>

                        <div id="block-social_quality" class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/40">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2.5">
                                <div>
                                    <label class="block text-xs sm:text-sm font-extrabold text-slate-800">3. Social Connection & Session Engagement (1–10 Scale)</label>
                                    <p class="text-xs text-slate-500 mt-0.5">Rate how connected and engaged you felt with colleagues during this session.</p>
                                </div>
                                <span class="text-[11px] font-extrabold text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full w-fit" id="scale10-badge">
                                    7 — Good: Warm peer interaction
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

                            <div class="grid grid-cols-5 sm:grid-cols-10 gap-1.5 mb-2.5">
                                @foreach($scaleDescriptions as $num => $desc)
                                    <label class="cursor-pointer text-center">
                                        <input type="radio"
                                               name="social_quality"
                                               value="{{ $num }}"
                                               class="scale10-radio"
                                               data-desc="{{ $desc }}"
                                               {{ (int) old('social_quality', 7) === $num ? 'checked' : '' }}
                                               required>
                                        <div class="scale10-label border border-slate-200 bg-white rounded-xl py-2 font-extrabold text-xs text-slate-700 hover:bg-slate-50 transition">
                                            {{ $num }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-extrabold text-slate-800 mb-1.5">4. Qualitative Feedback & Key Takeaways <span class="text-slate-400 font-normal">(Optional)</span></label>
                            <textarea name="comments"
                                      rows="2"
                                      placeholder="Share what worked well or suggestions for People & Culture..."
                                      class="w-full border border-slate-200 rounded-xl text-xs font-medium p-3.5 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">{{ old('comments') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-wrap justify-between items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400">Reward:</span>
                        <span class="text-xs font-extrabold bg-[#c99a2e]/20 text-amber-900 px-2.5 py-1 rounded-lg">
                            +{{ $registration->event->points_feedback ?? 10 }} Wellness Points
                        </span>
                    </div>
                    <button type="submit"
                            id="post-feedback-submit-btn"
                            class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 px-7 rounded-xl shadow-sm transition flex items-center gap-2">
                        <span>Submit Post-Event Feedback →</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const who5Fields = [
            { name: 'item_cheerful', label: 'Question 1 (Cheerful and in good spirits)' },
            { name: 'item_calm', label: 'Question 2 (Calm and relaxed)' },
            { name: 'item_active', label: 'Question 3 (Active and vigorous)' },
            { name: 'item_rested', label: 'Question 4 (Fresh and rested)' },
            { name: 'item_interested', label: 'Question 5 (Daily life filled with interesting things)' }
        ];

        const radios          = document.querySelectorAll('.who5-radio');
        const scoreDisplay    = document.getElementById('live-score');
        const postStroke      = document.getElementById('post-gauge-stroke');
        const postPercentText = document.getElementById('post-gauge-percent');
        const postStatusText  = document.getElementById('post-gauge-status');
        const interpDisplay   = document.getElementById('who5-interpretation');
        const counterPill     = document.getElementById('post-answered-counter');

        function updateWho5() {
            let total = 0;
            let answered = 0;

            who5Fields.forEach(item => {
                const selected = document.querySelector(`input[name="${item.name}"]:checked`);
                const block    = document.getElementById(`question-block-${item.name}`);
                const warn     = document.getElementById(`warn-${item.name}`);

                if (selected) {
                    total += parseInt(selected.value, 10);
                    answered++;
                    if (block) {
                        block.classList.remove('border-[#e03c31]', 'bg-rose-50/40');
                        block.classList.add('border-slate-100');
                    }
                    if (warn) warn.classList.add('hidden');
                }
            });

            const percent = Math.round((total / 25) * 100);
            scoreDisplay.innerText = total;
            postPercentText.innerText = percent + '%';
            postStroke.style.strokeDashoffset = 100 - percent;
            if (counterPill) counterPill.innerText = `${answered} / 5`;

            if (answered === 5) {
                document.getElementById('post-who5-warning-banner')?.classList.add('hidden');
            }

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
                    interpDisplay.className = 'text-xs font-bold text-rose-600 leading-snug';
                }
            } else if (total <= 18) {
                postStatusText.innerText = 'Moderate';
                postStatusText.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700';
                postStroke.setAttribute('stroke', '#d97706');
                if (answered === 5) {
                    interpDisplay.innerText = 'Score 13–18: Moderate wellbeing — continue engaging in regular wellness activities.';
                    interpDisplay.className = 'text-xs font-bold text-amber-700 leading-snug';
                }
            } else {
                postStatusText.innerText = 'Flourishing';
                postStatusText.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700';
                postStroke.setAttribute('stroke', '#10b981');
                if (answered === 5) {
                    interpDisplay.innerText = 'Score 19–25: Strong positive wellbeing and flourishing energy.';
                    interpDisplay.className = 'text-xs font-bold text-emerald-700 leading-snug';
                }
            }

            if (answered < 5) {
                interpDisplay.innerText = `${answered} of 5 items answered — complete the remaining questions to finalize your score.`;
                interpDisplay.className = 'text-xs text-slate-500 leading-snug';
            }
        }

        radios.forEach(r => r.addEventListener('change', updateWho5));
        updateWho5();

        // Client-Side Form Submission Validation with Explicit Warning Messages
        const form = document.getElementById('post-feedback-form');
        form.addEventListener('submit', function (e) {
            const missing = [];
            let firstMissingEl = null;

            who5Fields.forEach(item => {
                const selected = document.querySelector(`input[name="${item.name}"]:checked`);
                const block    = document.getElementById(`question-block-${item.name}`);
                const warn     = document.getElementById(`warn-${item.name}`);

                if (!selected) {
                    missing.push(item.label);
                    if (block) {
                        block.classList.remove('border-slate-100');
                        block.classList.add('border-[#e03c31]', 'bg-rose-50/40');
                    }
                    if (warn) warn.classList.remove('hidden');
                    if (!firstMissingEl) firstMissingEl = block;
                }
            });

            if (missing.length > 0) {
                e.preventDefault();
                const banner = document.getElementById('post-who5-warning-banner');
                const text   = document.getElementById('post-who5-warning-text');
                if (banner && text) {
                    text.innerText = `Warning: Please answer ${missing.join(', ')} before submitting your post-event feedback.`;
                    banner.classList.remove('hidden');
                }
                if (firstMissingEl) {
                    firstMissingEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            const btn = document.getElementById('post-feedback-submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
            }
        });

        const scaleRadios = document.querySelectorAll('.scale10-radio');
        const scaleBadge  = document.getElementById('scale10-badge');
        scaleRadios.forEach(r => {
            if (r.checked) scaleBadge.innerText = r.dataset.desc;
            r.addEventListener('change', function () {
                if (this.checked) scaleBadge.innerText = this.dataset.desc;
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