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
    $user = auth()->user();
    $spotsLeft = $event->spots_remaining ?? max(0, (int) $event->capacity - $event->registrations()->count());
    $totalPts = 5 + (int) $event->points_checkin + (int) $event->points_feedback;
@endphp

<div class="max-w-3xl mx-auto">

    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('events.index') }}" class="text-xs font-extrabold text-[#02338d] hover:underline inline-flex items-center gap-1">
            ← Back to Upcoming Events
        </a>
    </div>

    <!-- Event Summary Banner -->
    <div class="bg-gradient-to-r from-[#02338d] to-[#01225e] text-white rounded-3xl p-6 sm:p-8 shadow-sm mb-8">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            <span class="text-[10px] font-extrabold uppercase tracking-widest bg-white/15 px-3 py-1 rounded-full">
                {{ ucfirst($event->category) }} Wellness
            </span>
            <span class="text-xs font-extrabold bg-[#c99a2e] text-[#01225e] px-3 py-1 rounded-full">
                Up to +{{ $totalPts }} Total Pts
            </span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $event->name }}</h1>
        <p class="text-sm text-blue-100 mt-2">{{ $event->description }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6 pt-5 border-t border-white/15 text-xs">
            <div>
                <span class="text-blue-200 block uppercase text-[10px] font-bold">Date & Time</span>
                <span class="font-extrabold">{{ $event->starts_at->format('D, d M Y \a\t H:i') }}</span>
            </div>
            <div>
                <span class="text-blue-200 block uppercase text-[10px] font-bold">Venue</span>
                <span class="font-extrabold">{{ $event->location }}</span>
            </div>
            <div>
                <span class="text-blue-200 block uppercase text-[10px] font-bold">Availability</span>
                <span class="font-extrabold">{{ $spotsLeft }} of {{ $event->capacity }} spots open</span>
            </div>
        </div>
    </div>

    <!-- Server-Side Validation Warning -->
    @if($errors->any())
        <div class="mb-6 bg-amber-50 text-amber-950 px-5 py-4 rounded-2xl text-sm font-bold border-2 border-amber-400 flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
            </svg>
            <div>
                <p class="font-extrabold text-amber-900">Action Required Before Registering</p>
                <p class="text-xs mt-0.5">{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    <!-- Client-Side Instant Pre-WHO-5 Warning Banner -->
    <div id="pre-who5-warning-banner" class="hidden mb-6 bg-amber-50 text-amber-950 px-5 py-4 rounded-2xl text-sm font-bold border-2 border-amber-400 flex items-start gap-3 shadow-sm">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
        </svg>
        <div>
            <p class="font-extrabold text-amber-900" id="warning-banner-title">Pre-Event WHO-5 Check-In Incomplete</p>
            <p class="text-xs mt-0.5" id="warning-banner-text">Please answer all 5 Pre-Event WHO-5 wellbeing questions highlighted below before completing your registration.</p>
        </div>
    </div>

    <form id="registration-form"
          method="POST"
          action="{{ route('registrations.store', $event) }}"
          novalidate
          class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <div class="p-6 sm:p-8 space-y-8">

            <!-- Step 1: Pre-Event WHO-5 Wellbeing Baseline -->
            <div>
                <div class="flex flex-wrap items-center justify-between gap-2 pb-4 mb-4 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#02338d] bg-[#e8f0fe] px-2.5 py-1 rounded-full">
                            Step 1 · Required Leading Baseline
                        </span>
                        <h2 class="text-lg font-extrabold text-slate-900 mt-2">Pre-Event WHO-5 Wellbeing Check-In</h2>
                    </div>
                    <span class="text-xs font-extrabold text-slate-500" id="who5-progress-pill">0 of 5 Answered</span>
                </div>

                <p class="text-sm text-slate-600 mb-6">
                    Before registering, indicate how you have felt over the <strong>last two weeks</strong>. All 5 questions are mandatory so your pre-event baseline can be paired with your post-event feedback.
                </p>

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

                <div class="space-y-5 mb-6">
                    @foreach($questions as $field => $text)
                        <div id="question-block-{{ $field }}" class="p-4 rounded-2xl border border-transparent transition-all">
                            <div class="flex items-center justify-between gap-2 mb-2.5">
                                <label class="block text-sm font-bold text-slate-800">{{ $text }}</label>
                                <span id="missing-badge-{{ $field }}" class="hidden text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-100 border border-amber-300 px-2.5 py-0.5 rounded-full shrink-0">
                                    Required
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-6 gap-2">
                                @foreach($options as $value => $opt)
                                    <label class="cursor-pointer text-center">
                                        <input type="radio"
                                               name="{{ $field }}"
                                               value="{{ $value }}"
                                               class="who5-radio"
                                               {{ (string) old($field) === (string) $value ? 'checked' : '' }}>
                                        <div class="who5-label border border-slate-200 rounded-xl py-2.5 px-1.5 text-slate-600 hover:bg-slate-50 transition-all h-full flex flex-col justify-center">
                                            <div class="font-extrabold text-base">{{ $opt['title'] }}</div>
                                            <div class="text-[10px] leading-tight mt-0.5 font-semibold">{{ $opt['label'] }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Live Pre-Event WHO-5 Score Box -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800">Pre-Event Baseline Score</h3>
                        <p class="text-xs text-slate-500 mt-0.5" id="pre-who5-interpretation">Answer all 5 items above to calculate your pre-event baseline.</p>
                    </div>
                    <div class="text-2xl font-extrabold text-[#02338d] shrink-0">
                        <span id="pre-live-score">0</span><span class="text-base text-slate-400">/25</span>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Step 2: Participation Waiver & Data Consent -->
            <div id="waiver-section" class="p-4 rounded-2xl border border-transparent transition-all">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-900 bg-[#c99a2e]/20 px-2.5 py-1 rounded-full">
                    Step 2 · Waiver & Confirmation
                </span>
                <h2 class="text-lg font-extrabold text-slate-900 mt-2 mb-2">Participation Waiver & Wellness Data Consent</h2>
                <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 border border-slate-200 rounded-2xl p-4 mb-4">
                    I confirm that I am voluntarily participating in <strong>{{ $event->name }}</strong> organized by Strathmore University People & Culture, and I consent to my anonymized WHO-5 wellbeing scores being aggregated for institutional wellness evaluation.
                </p>

                <div class="space-y-4">
                    <div>
                        <label for="signature_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Digital Signature (Full Name)
                        </label>
                        <input type="text"
                               id="signature_name"
                               name="signature_name"
                               value="{{ old('signature_name', $user->name) }}"
                               class="w-full border border-slate-300 rounded-xl text-sm p-3 bg-slate-50 focus:ring-[#02338d] focus:border-[#02338d]">
                    </div>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox"
                               id="agree_checkbox"
                               name="agree"
                               value="1"
                               {{ old('agree') ? 'checked' : '' }}
                               class="mt-1 rounded border-slate-300 text-[#02338d] focus:ring-[#02338d]">
                        <span class="text-xs font-bold text-slate-700">
                            I agree to the participation waiver and submit my pre-event wellbeing baseline (+5 registration points).
                        </span>
                    </label>
                </div>
            </div>

        </div>

        <!-- Submit Footer -->
        <div class="bg-slate-50 px-6 sm:px-8 py-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
                Registering as <strong class="text-slate-800">{{ $user->name }}</strong> ({{ $user->staff_id }})
            </div>
            <button type="submit" id="submit-registration-btn" class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-6 py-3.5 rounded-xl shadow-sm transition">
                Complete Registration & Unlock Ticket →
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fields = ['item_cheerful', 'item_calm', 'item_active', 'item_rested', 'item_interested'];
        const form = document.getElementById('registration-form');
        const radios = document.querySelectorAll('.who5-radio');
        const scoreEl = document.getElementById('pre-live-score');
        const interpEl = document.getElementById('pre-who5-interpretation');
        const progressPill = document.getElementById('who5-progress-pill');
        const warningBanner = document.getElementById('pre-who5-warning-banner');
        const warningTitle = document.getElementById('warning-banner-title');
        const warningText = document.getElementById('warning-banner-text');

        function updatePreScore() {
            let total = 0;
            let count = 0;

            fields.forEach(name => {
                const checked = document.querySelector(`input[name="${name}"]:checked`);
                const block = document.getElementById(`question-block-${name}`);
                const badge = document.getElementById(`missing-badge-${name}`);

                if (checked) {
                    total += parseInt(checked.value, 10);
                    count++;
                    block.classList.remove('bg-amber-50/70', 'border-amber-300');
                    block.classList.add('border-transparent');
                    badge.classList.add('hidden');
                }
            });

            scoreEl.innerText = total;
            progressPill.innerText = `${count} of 5 Answered`;

            if (count === 5) {
                progressPill.className = 'text-xs font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full';
                warningBanner.classList.add('hidden');

                if (total < 13) {
                    interpEl.innerText = 'Baseline below 13: Indicates reduced wellbeing prior to the session.';
                    interpEl.className = 'text-xs font-bold text-rose-600 mt-0.5';
                } else if (total <= 18) {
                    interpEl.innerText = 'Baseline 13–18: Moderate pre-event wellbeing.';
                    interpEl.className = 'text-xs font-bold text-amber-700 mt-0.5';
                } else {
                    interpEl.innerText = 'Baseline 19–25: Strong positive pre-event wellbeing.';
                    interpEl.className = 'text-xs font-bold text-emerald-700 mt-0.5';
                }
            } else {
                progressPill.className = 'text-xs font-extrabold text-slate-500';
            }
        }

        radios.forEach(r => r.addEventListener('change', updatePreScore));
        updatePreScore();

        form.addEventListener('submit', function (e) {
            const missingFields = [];

            fields.forEach(name => {
                const checked = document.querySelector(`input[name="${name}"]:checked`);
                const block = document.getElementById(`question-block-${name}`);
                const badge = document.getElementById(`missing-badge-${name}`);

                if (!checked) {
                    missingFields.push(name);
                    block.classList.remove('border-transparent');
                    block.classList.add('bg-amber-50/70', 'border-amber-300');
                    badge.classList.remove('hidden');
                }
            });

            if (missingFields.length > 0) {
                e.preventDefault();
                warningTitle.innerText = `Warning: ${missingFields.length} Pre-Event WHO-5 Question${missingFields.length > 1 ? 's' : ''} Unanswered`;
                warningText.innerText = 'Please complete all highlighted Pre-Event WHO-5 wellbeing items before submitting your registration.';
                warningBanner.classList.remove('hidden');

                const firstMissing = document.getElementById(`question-block-${missingFields[0]}`);
                firstMissing.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            const sigInput = document.getElementById('signature_name');
            const agreeBox = document.getElementById('agree_checkbox');
            const waiverSection = document.getElementById('waiver-section');

            if (!sigInput.value.trim() || !agreeBox.checked) {
                e.preventDefault();
                warningTitle.innerText = 'Warning: Digital Signature & Waiver Consent Required';
                warningText.innerText = 'Please enter your full name and tick the waiver agreement box in Step 2.';
                warningBanner.classList.remove('hidden');
                waiverSection.classList.remove('border-transparent');
                waiverSection.classList.add('bg-amber-50/70', 'border-amber-300');
                waiverSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            const btn = document.getElementById('submit-registration-btn');
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            btn.innerText = 'Confirming Registration...';
        });
    });
</script>
@endpush