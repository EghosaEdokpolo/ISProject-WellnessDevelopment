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
<div class="max-w-7xl mx-auto px-4 py-4">

    <div class="mb-6">
        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
            Pre-Event Baseline • Step 1 of 2
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
            Pre-Event WHO-5 Wellbeing Check-In
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            {{ $registration->event->name ?? 'Wellness Session' }} • Establish your baseline wellbeing score before signing your participation waiver.
        </p>
    </div>

    @if(session('warning') || $errors->any())
        <div class="mb-6 bg-amber-50 text-amber-950 px-5 py-4 rounded-2xl text-xs font-extrabold border-2 border-amber-400 shadow-sm">
            {{ session('warning') ?? $errors->first() }}
        </div>
    @endif

    <div id="baseline-warning-banner" class="hidden mb-6 bg-amber-50 text-amber-950 px-5 py-4 rounded-2xl text-xs font-extrabold border-2 border-[#e03c31] shadow-sm flex items-start gap-3">
        <svg class="w-5 h-5 text-[#e03c31] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
        </svg>
        <div>
            <span class="block text-[#e03c31] uppercase tracking-wider text-[11px]">Action Required</span>
            <p id="baseline-warning-text" class="mt-0.5 font-bold text-slate-800"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Compact Gauge -->
        <div class="lg:col-span-4 lg:sticky lg:top-20">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <h2 class="text-sm font-extrabold text-slate-900">Live Pre-Event WHO-5</h2>
                    <span id="base-gauge-status" class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500">
                        Pending
                    </span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative w-24 h-24 shrink-0">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9155" fill="transparent" stroke="#f1f5f9" stroke-width="3.2"></circle>
                            <circle id="base-gauge-stroke"
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
                                <span id="base-live-score">0</span><span class="text-[11px] font-normal text-slate-400">/25</span>
                            </span>
                            <span id="base-gauge-percent" class="text-[10px] font-bold text-slate-500">0%</span>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 leading-snug" id="base-who5-interpretation">
                        Select your responses on the right to see your live baseline score.
                    </p>
                </div>

                <div class="mt-4 pt-3.5 border-t border-slate-100 space-y-1.5 text-[11px] font-bold text-slate-600">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 bg-red-500 rounded-full"></span><span>Reduced</span></span>
                        <span class="text-slate-400">0 – 12</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 bg-amber-600 rounded-full"></span><span>Moderate</span></span>
                        <span class="text-slate-400">13 – 18</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 bg-emerald-600 rounded-full"></span><span>Flourishing</span></span>
                        <span class="text-slate-400">19 – 25</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Questions Form -->
        <div class="lg:col-span-8">
            <form method="POST"
                  id="baseline-who5-form"
                  action="{{ route('registrations.who5.store', $registration) }}"
                  novalidate
                  class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                @csrf

                <div class="p-6 sm:p-7 space-y-5">
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

                    @foreach($questions as $field => $text)
                        <div id="base-block-{{ $field }}" class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/40 transition-all">
                            <div class="flex items-center justify-between gap-2 mb-2.5">
                                <label class="block text-xs sm:text-sm font-extrabold text-slate-800">{{ $text }}</label>
                                <span id="base-warn-{{ $field }}" class="hidden text-[11px] font-extrabold text-[#e03c31] bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full shrink-0">
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

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex justify-end">
                    <button type="submit" class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 px-7 rounded-xl shadow-sm transition">
                        Save Baseline & Continue to Waiver →
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

        const radios  = document.querySelectorAll('.who5-radio');
        const scoreEl = document.getElementById('base-live-score');
        const stroke  = document.getElementById('base-gauge-stroke');
        const pctEl   = document.getElementById('base-gauge-percent');
        const status  = document.getElementById('base-gauge-status');
        const interp  = document.getElementById('base-who5-interpretation');

        function updateBaseline() {
            let total = 0;
            let answered = 0;

            who5Fields.forEach(item => {
                const selected = document.querySelector(`input[name="${item.name}"]:checked`);
                const block    = document.getElementById(`base-block-${item.name}`);
                const warn     = document.getElementById(`base-warn-${item.name}`);

                if (selected) {
                    total += parseInt(selected.value, 10);
                    answered++;
                    block?.classList.remove('border-[#e03c31]', 'bg-rose-50/40');
                    warn?.classList.add('hidden');
                }
            });

            const pct = Math.round((total / 25) * 100);
            scoreEl.innerText = total;
            pctEl.innerText   = pct + '%';
            stroke.style.strokeDashoffset = 100 - pct;

            if (answered === 0) return;

            if (total < 13) {
                status.innerText = 'Reduced';
                status.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-600';
                stroke.setAttribute('stroke', '#ef4444');
            } else if (total <= 18) {
                status.innerText = 'Moderate';
                status.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700';
                stroke.setAttribute('stroke', '#d97706');
            } else {
                status.innerText = 'Flourishing';
                status.className = 'text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700';
                stroke.setAttribute('stroke', '#10b981');
            }

            interp.innerText = answered < 5
                ? `${answered} of 5 items answered — complete the remaining items.`
                : `Baseline score recorded: ${total}/25 (${pct}%).`;
        }

        radios.forEach(r => r.addEventListener('change', updateBaseline));
        updateBaseline();

        document.getElementById('baseline-who5-form').addEventListener('submit', function (e) {
            const missing = [];
            let firstEl = null;

            who5Fields.forEach(item => {
                const selected = document.querySelector(`input[name="${item.name}"]:checked`);
                const block    = document.getElementById(`base-block-${item.name}`);
                const warn     = document.getElementById(`base-warn-${item.name}`);
                if (!selected) {
                    missing.push(item.label);
                    block?.classList.add('border-[#e03c31]', 'bg-rose-50/40');
                    warn?.classList.remove('hidden');
                    if (!firstEl) firstEl = block;
                }
            });

            if (missing.length > 0) {
                e.preventDefault();
                const banner = document.getElementById('baseline-warning-banner');
                const text   = document.getElementById('baseline-warning-text');
                if (banner && text) {
                    text.innerText = `Warning: Please answer ${missing.join(', ')} before continuing.`;
                    banner.classList.remove('hidden');
                }
                firstEl?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
</script>
@endpush