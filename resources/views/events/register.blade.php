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
</style>
@endpush

@section('content')
@php
    $tz         = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $eventStart = $event->starts_at->copy()->shiftTimezone($tz);
@endphp

<div class="max-w-7xl mx-auto px-4 py-4">

    <!-- Top Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
                    {{ strtoupper($event->category) }} • Pre-Event Onboarding
                </span>
                <span class="text-[11px] font-extrabold text-[#8a6514] bg-[#f5eccd] px-2.5 py-0.5 rounded-full">
                    +5 Pts On Completion
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">{{ $event->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 italic mt-0.5">
                {{ $eventStart->format('l, d M Y \a\t h:i A') }} EAT • {{ $event->location }}
            </p>
        </div>

        <a href="{{ route('events.index') }}" class="text-xs font-extrabold text-slate-500 hover:text-[#02338d] transition">
            ← Back to Events Hub
        </a>
    </div>

    <!-- Server-Side Validation Warning Banner -->
    @if($errors->any() || session('warning'))
        <div class="mb-6 bg-amber-50 text-amber-950 px-5 py-4 rounded-2xl text-xs font-extrabold border-2 border-amber-400 shadow-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-[#e03c31] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
            </svg>
            <div>
                <span class="block text-[#e03c31] uppercase tracking-wider text-[11px]">Incomplete Pre-Event Registration Warning</span>
                @if(session('warning'))
                    <p class="mt-0.5 font-bold">{{ session('warning') }}</p>
                @endif
                @if($errors->any())
                    <ul class="list-disc list-inside mt-1 space-y-0.5 font-bold">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    <!-- Client-Side Interactive Warning Banner -->
    <div id="pre-who5-warning-banner" class="hidden mb-6 bg-amber-50 text-amber-950 px-5 py-4 rounded-2xl text-xs font-extrabold border-2 border-[#e03c31] shadow-sm flex items-start gap-3">
        <svg class="w-5 h-5 text-[#e03c31] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
        </svg>
        <div>
            <span class="block text-[#e03c31] uppercase tracking-wider text-[11px]">Action Required Before Confirming Registration</span>
            <p id="pre-who5-warning-text" class="mt-0.5 font-bold text-slate-800"></p>
        </div>
    </div>

    <!-- Space-Efficient 12-Column Grid: Left Compact Graph (4 Cols) + Right Pre-WHO-5 & Waiver Form (8 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN: Compact Sticky Live Pre-Event WHO-5 Donut Graph -->
        <div class="lg:col-span-4 lg:sticky lg:top-20 space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <h2 class="text-sm font-extrabold text-slate-900">Live Pre-Event WHO-5</h2>
                    <span id="pre-gauge-status" class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500">
                        Pending
                    </span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative w-24 h-24 shrink-0">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9155" fill="transparent" stroke="#f1f5f9" stroke-width="3.2"></circle>
                            <circle id="pre-gauge-stroke"
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
                                <span id="pre-live-score">0</span><span class="text-[11px] font-normal text-slate-400">/25</span>
                            </span>
                            <span id="pre-gauge-percent" class="text-[10px] font-bold text-slate-500">0%</span>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-slate-500 leading-snug" id="pre-who5-interpretation">
                            Answer all 5 baseline items on the right to see your live Pre-Event WHO-5 score.
                        </p>
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
                    <span class="text-slate-400">Baseline Answered</span>
                    <span id="pre-answered-counter" class="text-[#02338d] bg-[#e8f0fe] px-2.5 py-0.5 rounded-full">0 / 5</span>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Pre-Event WHO-5 Survey + Digital Waiver Form -->
        <div class="lg:col-span-8">
            <form method="POST"
                  id="pre-registration-form"
                  action="{{ route('registrations.store', $event) }}"
                  novalidate
                  class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                @csrf

                <div class="p-6 sm:p-7">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                        <h2 class="text-base font-extrabold text-slate-900">Step 1: Pre-Event WHO-5 Wellbeing Baseline</h2>
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
                            <div id="pre-block-{{ $field }}"
                                 class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/40 transition-all">
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <label class="block text-xs sm:text-sm font-extrabold text-slate-800">{{ $text }}</label>
                                    <span id="pre-warn-{{ $field }}" class="hidden text-[11px] font-extrabold text-[#e03c31] bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full shrink-0">
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
                            </div>
                        @endforeach
                    </div>

                    <hr class="border-slate-100 mb-6">

                    <!-- Step 2: Mandatory Digital Waiver -->
                    <div class="space-y-4">
                        <h2 class="text-base font-extrabold text-slate-900">Step 2: Mandatory Participation & Indemnity Waiver</h2>

                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs text-slate-600 leading-relaxed italic">
                            I, the undersigned staff member of Strathmore University, acknowledge that participation in this wellness session is voluntary. I understand that physical and wellness activities involve inherent risks and hereby indemnify Strathmore University, its People & Culture department, officers, and facilitators from any claims arising from my participation.
                        </div>

                        <div id="pre-block-signature" class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/40">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-600">
                                    Digital Signature (Type Full Name) <span class="text-rose-500">*</span>
                                </label>
                                <span id="pre-warn-signature" class="hidden text-[11px] font-extrabold text-[#e03c31]">
                                    ⚠️ Signature Required
                                </span>
                            </div>
                            <input type="text"
                                   name="signature_name"
                                   id="signature_name_input"
                                   value="{{ old('signature_name', auth()->user()->name) }}"
                                   placeholder="e.g., {{ auth()->user()->name }}"
                                   required
                                   class="w-full border border-slate-200 rounded-xl text-xs font-bold p-3 bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                        </div>

                        <div id="pre-block-agree" class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <input type="checkbox"
                                       name="agree"
                                       id="agree_checkbox"
                                       value="1"
                                       {{ old('agree') ? 'checked' : '' }}
                                       required
                                       class="mt-0.5 w-4 h-4 text-[#02338d] border-slate-300 rounded focus:ring-[#02338d] cursor-pointer">
                                <label for="agree_checkbox" class="text-xs font-bold text-slate-700 cursor-pointer select-none">
                                    I have read, understood, and agree to the Strathmore University participation waiver and data consent above.
                                </label>
                            </div>
                            <span id="pre-warn-agree" class="hidden text-[11px] font-extrabold text-[#e03c31] shrink-0">
                                ⚠️ Must Agree
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-wrap justify-between items-center gap-3">
                    <span class="text-xs font-extrabold text-[#02338d]">
                        Onboarding Reward: +5 Pts
                    </span>
                    <button type="submit"
                            id="pre-register-submit-btn"
                            class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 px-7 rounded-xl shadow-sm transition">
                        Complete Registration & Claim Pass (+5 Pts) →
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

        const radios        = document.querySelectorAll('.who5-radio');
        const scoreDisplay  = document.getElementById('pre-live-score');
        const preStroke     = document.getElementById('pre-gauge-stroke');
        const prePercent    = document.getElementById('pre-gauge-percent');
        const preStatus     = document.getElementById('pre-gauge-status');
        const interpDisplay = document.getElementById('pre-who5-interpretation');
        const counterPill   = document.getElementById('pre-answered-counter');

        function updatePreWho5() {
            let total = 0;
            let answered = 0;

            who5Fields.forEach(item => {
                const selected = document.querySelector(`input[name="${item.name}"]:checked`);
                const block    = document.getElementById(`pre-block-${item.name}`);
                const warn     = document.getElementById(`pre-warn-${item.name}`);

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
            prePercent.innerText = percent + '%';
            preStroke.style.strokeDashoffset = 100 - percent;
            if (counterPill) counterPill.innerText = `${answered} / 5`;

            if (answered === 0) {
                preStatus.innerText = 'Pending';
                preStatus.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500';
                preStroke.setAttribute('stroke', '#02338d');
                return;
            }

            if (total < 13) {
                preStatus.innerText = 'Reduced';
                preStatus.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-600';
                preStroke.setAttribute('stroke', '#ef4444');
                if (answered === 5) {
                    interpDisplay.innerText = 'Score below 13: Indicates reduced baseline wellbeing.';
                    interpDisplay.className = 'text-xs font-bold text-rose-600 leading-snug';
                }
            } else if (total <= 18) {
                preStatus.innerText = 'Moderate';
                preStatus.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700';
                preStroke.setAttribute('stroke', '#d97706');
                if (answered === 5) {
                    interpDisplay.innerText = 'Score 13–18: Moderate baseline wellbeing.';
                    interpDisplay.className = 'text-xs font-bold text-amber-700 leading-snug';
                }
            } else {
                preStatus.innerText = 'Flourishing';
                preStatus.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700';
                preStroke.setAttribute('stroke', '#10b981');
                if (answered === 5) {
                    interpDisplay.innerText = 'Score 19–25: Strong flourishing baseline wellbeing.';
                    interpDisplay.className = 'text-xs font-bold text-emerald-700 leading-snug';
                }
            }

            if (answered < 5) {
                interpDisplay.innerText = `${answered} of 5 items answered — complete the remaining items.`;
                interpDisplay.className = 'text-xs text-slate-500 leading-snug';
            }
        }

        radios.forEach(r => r.addEventListener('change', updatePreWho5));
        updatePreWho5();

        const form = document.getElementById('pre-registration-form');
        form.addEventListener('submit', function (e) {
            const missing = [];
            let firstMissingEl = null;

            who5Fields.forEach(item => {
                const selected = document.querySelector(`input[name="${item.name}"]:checked`);
                const block    = document.getElementById(`pre-block-${item.name}`);
                const warn     = document.getElementById(`pre-warn-${item.name}`);

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

            const sigInput = document.getElementById('signature_name_input');
            const sigBlock = document.getElementById('pre-block-signature');
            const sigWarn  = document.getElementById('pre-warn-signature');
            if (!sigInput || sigInput.value.trim() === '') {
                missing.push('Digital Signature (Full Name)');
                sigBlock?.classList.add('border-[#e03c31]', 'bg-rose-50/40');
                sigWarn?.classList.remove('hidden');
                if (!firstMissingEl) firstMissingEl = sigBlock;
            } else {
                sigBlock?.classList.remove('border-[#e03c31]', 'bg-rose-50/40');
                sigWarn?.classList.add('hidden');
            }

            const agreeBox   = document.getElementById('agree_checkbox');
            const agreeBlock = document.getElementById('pre-block-agree');
            const agreeWarn  = document.getElementById('pre-warn-agree');
            if (!agreeBox || !agreeBox.checked) {
                missing.push('Participation Waiver Agreement Checkbox');
                agreeBlock?.classList.add('border-[#e03c31]', 'bg-rose-50/40');
                agreeWarn?.classList.remove('hidden');
                if (!firstMissingEl) firstMissingEl = agreeBlock;
            } else {
                agreeBlock?.classList.remove('border-[#e03c31]', 'bg-rose-50/40');
                agreeWarn?.classList.add('hidden');
            }

            if (missing.length > 0) {
                e.preventDefault();
                const banner = document.getElementById('pre-who5-warning-banner');
                const text   = document.getElementById('pre-who5-warning-text');
                if (banner && text) {
                    text.innerText = `Warning: Please complete ${missing.join(', ')} before continuing.`;
                    banner.classList.remove('hidden');
                }
                if (firstMissingEl) {
                    firstMissingEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            const btn = document.getElementById('pre-register-submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
            }
        });
    });
</script>
@endpush