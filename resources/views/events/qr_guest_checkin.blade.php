@extends('layouts.app')

@section('content')
@php
    $tz              = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $graceMins       = (int) config('wellloop.checkin.late_grace_minutes', 15);
    $eventStart      = $gate['starts_at'];
    $gateOpensAt     = $gate['opens_at'];
    $gateClosesAt    = $gate['closes_at'];
    $isStaffGateOpen = (bool) ($gate['is_open'] ?? false);
    $isLateGrace     = ($gate['status'] ?? '') === 'late_grace';
    $isTooEarly      = ($gate['status'] ?? '') === 'locked';

    $isGuestOpen     = (bool) ($guestWindow['is_open'] ?? false);
    $guestWindowEnd  = $guestWindow['ends_at'] ?? $eventStart->copy()->addHours(2);

    $resolvedTab = old('checkin_mode', $activeTab ?? 'staff');
    if ($errors->hasAny(['full_name', 'email', 'phone', 'guest_type', 'waiver_accepted']) || $pendingOtp !== null) {
        $resolvedTab = 'guest';
    }
@endphp

<div class="max-w-lg mx-auto px-2 sm:px-4 py-4 sm:py-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden">
        <!-- Navy Institutional Header -->
        <div class="bg-[#02338d] text-white px-5 sm:px-6 py-6 sm:py-7 text-center">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#c99a2e] block">
                Strathmore University • Venue QR Check-In
            </span>
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight mt-1">{{ $event->name }}</h1>
            <p class="text-xs text-blue-100 italic mt-1">
                {{ $eventStart->format('l, d M Y \a\t h:i A') }} EAT • {{ $event->location }}
            </p>
        </div>

        <div class="p-5 sm:p-7 space-y-5">

            @if($outcome !== null && $attendee !== null)
                <!-- STAFF CHECK-IN CONFIRMED -->
                <div class="text-center space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full {{ $outcome['already_checked_in'] ? 'bg-blue-50 text-[#02338d]' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $outcome['already_checked_in'] ? 'Already Checked In' : 'Staff Attendance Verified ✓' }}
                        </span>
                        <h2 class="text-xl font-extrabold text-slate-900 mt-2">Welcome, {{ $attendee->name }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Staff ID: <strong class="text-[#02338d]">{{ $attendee->staff_id }}</strong> · {{ $attendee->department }}
                        </p>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2 text-xs">
                        @if(! $outcome['already_checked_in'])
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 font-semibold">Check-In Points Earned</span>
                                <span class="font-extrabold text-emerald-600">+{{ $outcome['points_earned'] }} pts</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">Updated Points Balance</span>
                            <span class="font-extrabold text-[#02338d]">{{ number_format((int) $attendee->points_balance) }} pts</span>
                        </div>
                        @if($outcome['is_late'])
                            <p class="text-[11px] font-bold text-amber-700 pt-2 border-t border-slate-200">
                                Checked in during the {{ $graceMins }}-minute late grace window ({{ $outcome['minutes_late'] }} min late; half points awarded).
                            </p>
                        @endif
                    </div>

                    @if($outcome['needs_onboarding'])
                        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3.5 text-xs text-amber-900 font-semibold">
                            You were auto-registered as a walk-in attendee. Log in to WellLoop to complete your Pre-Event WHO-5 baseline, waiver, and Post-Event feedback.
                        </div>
                    @endif

                    <a href="{{ route('login') }}"
                       class="w-full block text-center bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                        Log In to Staff Portal →
                    </a>
                </div>

            @elseif($guestOutcome !== null)
                <!-- EXTERNAL VISITOR / GUEST VERIFIED & CHECKED IN -->
                @php $g = $guestOutcome['guest']; @endphp
                <div class="text-center space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full {{ $guestOutcome['already_checked_in'] ? 'bg-blue-50 text-[#02338d]' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $guestOutcome['already_checked_in'] ? 'Visitor Already Signed In' : 'OTP Verified • Check-In Confirmed ✓' }}
                        </span>
                        <h2 class="text-xl font-extrabold text-slate-900 mt-2">Welcome, {{ $g->full_name }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $g->guest_type_label }}@if($g->organization) · {{ $g->organization }}@endif
                        </p>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2 text-xs text-left">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-slate-500 font-semibold">Visitor Pass Ref</span>
                            <span class="font-extrabold text-[#02338d]">GUEST-{{ str_pad((string) $g->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-slate-500 font-semibold">Identity Verification</span>
                            <span class="font-extrabold text-emerald-600">Email OTP Verified ✓</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-slate-500 font-semibold">Checked In At</span>
                            <span class="font-bold text-slate-800">{{ $g->checked_in_at?->copy()->setTimezone($tz)->format('h:i A, d M Y') }} EAT</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-slate-500 font-semibold">KDPA 2019 Notice</span>
                            <span class="font-extrabold text-emerald-600">Accepted ✓</span>
                        </div>
                    </div>

                    <button type="button"
                            onclick="openKdpaModal()"
                            class="text-xs font-extrabold text-[#02338d] hover:underline">
                        Review Kenya Data Protection Act (2019) Rights ↗
                    </button>
                </div>

            @elseif($pendingOtp !== null)
                <!-- STEP 2: 6-DIGIT EMAIL OTP VERIFICATION SCREEN -->
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="w-12 h-12 rounded-2xl bg-[#e8f0fe] text-[#02338d] flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
                            Step 2 of 2 • Verify Your Identity
                        </span>
                        <h2 class="text-lg font-extrabold text-slate-900 mt-2">Enter 6-Digit Verification Code</h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Sent to <strong class="text-slate-800">{{ $pendingOtp['masked_email'] }}</strong> for {{ $pendingOtp['full_name'] }}.
                        </p>
                    </div>

                    @if($scanNotice)
                        <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold border border-emerald-200">
                            {{ $scanNotice }}
                        </div>
                    @endif

                    @if(!empty($pendingOtp['fallback_notice']))
                        <div class="bg-amber-50 border border-amber-300 rounded-2xl p-4 text-xs text-amber-950 space-y-1.5">
                            <p class="font-extrabold">Local / Dev Mail Mode Notice</p>
                            <p class="text-[11px] leading-relaxed">{{ $pendingOtp['fallback_notice'] }}</p>
                            @if(!empty($pendingOtp['demo_otp']))
                                <div class="pt-1 flex items-center justify-between bg-white border border-amber-200 rounded-xl px-3.5 py-2">
                                    <span class="text-[11px] font-bold text-slate-600">Verification Code:</span>
                                    <span class="text-base font-extrabold tracking-[0.25em] text-[#02338d]">{{ $pendingOtp['demo_otp'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($scanError)
                        <div class="bg-rose-50 text-rose-700 px-4 py-3 rounded-2xl text-xs font-extrabold border border-rose-200">
                            {{ $scanError }}
                        </div>
                    @endif

                    <form method="POST" action="{{ $guestOtpUrl }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="otp_token" value="{{ $pendingOtp['token'] }}">

                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1.5 text-center">
                                6-Digit OTP Code
                            </label>
                            <input type="text"
                                   name="otp_code"
                                   inputmode="numeric"
                                   pattern="[0-9]{6}"
                                   maxlength="6"
                                   autocomplete="one-time-code"
                                   required
                                   autofocus
                                   placeholder="000000"
                                   class="w-full border-2 border-slate-300 rounded-2xl text-center text-2xl font-extrabold tracking-[0.45em] text-[#02338d] py-3.5 bg-slate-50 focus:bg-white focus:border-[#02338d] focus:ring-[#02338d]">
                            <p id="otp-expiry-countdown"
                               data-expires-at="{{ $pendingOtp['expires_at_iso'] }}"
                               class="text-[11px] text-center text-slate-400 font-semibold mt-1.5">
                                Code expires in 10:00
                            </p>
                        </div>

                        <button type="submit"
                                class="w-full bg-[#02338d] hover:bg-[#01225e] text-white text-xs sm:text-sm font-extrabold py-3.5 rounded-xl shadow-sm transition">
                            Verify Code & Complete Check-In →
                        </button>
                    </form>

                    <!-- Resend OTP & Edit Details -->
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                        <form method="POST" action="{{ $guestResendUrl }}">
                            @csrf
                            <input type="hidden" name="otp_token" value="{{ $pendingOtp['token'] }}">
                            <button type="submit"
                                    id="resend-otp-btn"
                                    data-wait="{{ (int) $pendingOtp['resend_wait_seconds'] }}"
                                    @disabled((int) $pendingOtp['resend_wait_seconds'] > 0)
                                    class="font-extrabold text-[#02338d] hover:underline disabled:text-slate-400 disabled:no-underline disabled:cursor-not-allowed">
                                {{ (int) $pendingOtp['resend_wait_seconds'] > 0 ? 'Resend code in ' . (int) $pendingOtp['resend_wait_seconds'] . 's' : 'Resend Verification Code' }}
                            </button>
                        </form>

                        <a href="{{ request()->fullUrlWithQuery(['mode' => 'guest']) }}"
                           class="font-bold text-slate-500 hover:text-slate-800">
                            ← Edit Details
                        </a>
                    </div>
                </div>

            @else
                <!-- DUAL-TAB SWITCHER: STRATHMORE STAFF vs EXTERNAL VISITOR -->
                <div class="grid grid-cols-2 gap-1.5 bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
                    <button type="button"
                            id="tab-btn-staff"
                            onclick="switchCheckInTab('staff')"
                            class="py-2.5 px-3 rounded-xl text-xs font-extrabold transition {{ $resolvedTab === 'staff' ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        Strathmore Staff
                    </button>
                    <button type="button"
                            id="tab-btn-guest"
                            onclick="switchCheckInTab('guest')"
                            class="py-2.5 px-3 rounded-xl text-xs font-extrabold transition {{ $resolvedTab === 'guest' ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        External Visitor / Guest
                    </button>
                </div>

                @if($scanError || $errors->any())
                    <div class="bg-rose-50 text-rose-700 px-4 py-3.5 rounded-2xl text-xs font-extrabold border border-rose-200 leading-relaxed">
                        {{ $scanError ?? $errors->first() }}
                    </div>
                @endif

                <!-- PANEL 1: STRATHMORE STAFF CHECK-IN -->
                <div id="panel-staff" class="space-y-4 {{ $resolvedTab === 'staff' ? '' : 'hidden' }}">
                    @if($isStaffGateOpen)
                        <div class="rounded-2xl p-4 text-center border {{ $isLateGrace ? 'bg-amber-50 border-amber-200' : 'bg-emerald-50 border-emerald-200' }}">
                            <p class="text-xs font-extrabold {{ $isLateGrace ? 'text-amber-900' : 'text-emerald-900' }}">
                                {{ $isLateGrace ? 'Late Grace Active (Half Points until ' . $gateClosesAt->format('H:i') . ' EAT)' : 'Staff Check-In Gate Open (100% Points)' }}
                            </p>
                            <p class="text-[11px] {{ $isLateGrace ? 'text-amber-700' : 'text-emerald-700' }} mt-0.5">
                                Enter your Strathmore Staff ID to verify attendance and credit your points immediately.
                            </p>
                        </div>

                        <form method="POST" action="{{ $verifyUrl }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="checkin_mode" value="staff">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">
                                    Strathmore Staff ID <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       name="staff_id"
                                       value="{{ old('staff_id') }}"
                                       required
                                       placeholder="e.g., 000004"
                                       class="w-full border border-slate-300 rounded-xl text-sm font-extrabold text-slate-900 p-3.5 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d] tracking-wider">
                            </div>

                            <button type="submit"
                                    class="w-full bg-[#02338d] hover:bg-[#01225e] text-white text-xs sm:text-sm font-extrabold py-3.5 rounded-xl shadow-sm transition">
                                Verify Staff ID & Check In →
                            </button>
                        </form>
                    @elseif($isTooEarly)
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center space-y-1.5">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full">
                                Gate Not Yet Open
                            </span>
                            <p class="text-xs font-extrabold text-slate-800 pt-1">
                                Check-in opens at {{ $gateOpensAt->format('D, d M Y \a\t h:i A') }} EAT
                            </p>
                            <p class="text-[11px] text-slate-500">
                                Please scan again within 1 hour of the session start time.
                            </p>
                        </div>
                    @else
                        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-center space-y-1.5">
                            <p class="text-xs font-extrabold text-rose-800">Staff Points Check-In Closed</p>
                            <p class="text-[11px] text-rose-600">
                                The {{ $graceMins }}-minute late grace window ended at {{ $gateClosesAt->format('h:i A') }} EAT (0 points after cutoff). External guests may still sign in on the <strong>External Visitor</strong> tab.
                            </p>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-100 text-center">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-[#02338d] transition">
                            Already have a WellLoop account? Log in to Staff Portal
                        </a>
                    </div>
                </div>

                <!-- PANEL 2: EXTERNAL VISITOR / GUEST CHECK-IN (STEP 1 -> OTP) -->
                <div id="panel-guest" class="space-y-4 {{ $resolvedTab === 'guest' ? '' : 'hidden' }}">
                    @if($isGuestOpen)
                        <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-4 text-xs text-slate-700 leading-relaxed">
                            <span class="font-extrabold text-[#02338d] block">Step 1 of 2 • Visitor Sign-In & Email OTP</span>
                            For guest speakers, external partners, visiting faculty, staff guests, or new hires awaiting a Staff ID. A 6-digit OTP will be emailed to confirm your identity. Open until <strong>{{ $guestWindowEnd->format('h:i A') }} EAT</strong>.
                        </div>

                        <form method="POST" action="{{ $guestCheckInUrl }}" class="space-y-3.5">
                            @csrf
                            <input type="hidden" name="checkin_mode" value="guest">

                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">
                                    Full Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       name="full_name"
                                       value="{{ old('full_name') }}"
                                       required
                                       placeholder="e.g., Dr. Grace Mutua"
                                       class="w-full border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">
                                        Email Address (For OTP) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           placeholder="visitor@example.com"
                                           class="w-full border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">
                                        Phone Number <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="tel"
                                           name="phone"
                                           value="{{ old('phone') }}"
                                           required
                                           placeholder="+254 7XX XXX XXX"
                                           class="w-full border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">
                                    Visitor Category <span class="text-rose-500">*</span>
                                </label>
                                <select name="guest_type"
                                        required
                                        class="w-full border border-slate-300 rounded-xl text-xs font-bold text-slate-900 p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                                    <option value="external_visitor" @selected(old('guest_type') === 'external_visitor')>External Visitor / Guest</option>
                                    <option value="facilitator" @selected(old('guest_type') === 'facilitator')>Facilitator / Guest Speaker</option>
                                    <option value="partner_vendor" @selected(old('guest_type') === 'partner_vendor')>External Partner / SACCO / Clinic</option>
                                    <option value="visiting_faculty" @selected(old('guest_type') === 'visiting_faculty')>Visiting Faculty / Researcher</option>
                                    <option value="staff_family" @selected(old('guest_type') === 'staff_family')>Staff Spouse / Family Guest</option>
                                    <option value="new_hire_pending" @selected(old('guest_type') === 'new_hire_pending')>New Hire / Intern (Pending Staff ID)</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">
                                        Organization / Affiliation
                                    </label>
                                    <input type="text"
                                           name="organization"
                                           value="{{ old('organization') }}"
                                           placeholder="e.g., Chiromo Hospital / Self"
                                           class="w-full border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">
                                        Host / Invited By
                                    </label>
                                    <input type="text"
                                           name="invited_by"
                                           value="{{ old('invited_by') }}"
                                           placeholder="e.g., People & Culture"
                                           class="w-full border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 p-3 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                                </div>
                            </div>

                            <!-- Viewable KDPA 2019 Consent Box -->
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 space-y-2.5">
                                <label class="flex items-start gap-2.5 cursor-pointer">
                                    <input type="checkbox"
                                           name="waiver_accepted"
                                           value="1"
                                           @checked(old('waiver_accepted'))
                                           required
                                           class="mt-0.5 rounded border-slate-300 text-[#02338d] focus:ring-[#02338d]">
                                    <span class="text-[11px] text-slate-600 leading-relaxed">
                                        I confirm voluntary participation in this Strathmore University wellness session and give explicit consent for my details to be processed for attendance & campus safety under the
                                        <button type="button"
                                                onclick="openKdpaModal()"
                                                class="font-extrabold text-[#02338d] underline hover:text-[#01225e]">
                                            Kenya Data Protection Act (No. 24 of 2019)
                                        </button>.
                                    </span>
                                </label>

                                <div class="pl-6 flex flex-wrap items-center gap-3">
                                    <button type="button"
                                            onclick="openKdpaModal()"
                                            class="inline-flex items-center gap-1 text-[11px] font-extrabold text-[#02338d] bg-white border border-[#02338d]/20 hover:bg-[#e8f0fe] px-2.5 py-1 rounded-lg transition">
                                        <span>Read Privacy Notice & Your Rights</span>
                                    </button>
                                    <a href="https://www.odpc.go.ke/wp-content/uploads/2024/02/TheDataProtectionAct__No24of2019.pdf"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="text-[11px] font-bold text-slate-500 hover:text-[#02338d] underline">
                                        Official Act PDF (ODPC) ↗
                                    </a>
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full bg-[#c99a2e] hover:bg-[#b58924] text-[#01225e] text-xs sm:text-sm font-extrabold py-3.5 rounded-xl shadow-sm transition">
                                Send 6-Digit Verification Code →
                            </button>
                        </form>
                    @else
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center space-y-1.5">
                            <p class="text-xs font-extrabold text-slate-800">Visitor Check-In Unavailable</p>
                            <p class="text-[11px] text-slate-500">
                                {{ $guestWindow['message'] ?? 'Visitor sign-in is only open during the active session window.' }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>

<!-- KENYA DATA PROTECTION ACT (NO. 24 OF 2019) MODAL -->
<div id="kdpa-modal"
     onclick="if (event.target === this) closeKdpaModal()"
     class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-xl w-full max-h-[88vh] flex flex-col shadow-2xl overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-[#02338d] text-white px-5 sm:px-6 py-4 flex items-center justify-between gap-3 shrink-0">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#c99a2e] block">
                    Republic of Kenya • Act No. 24 of 2019
                </span>
                <h3 class="text-base sm:text-lg font-extrabold leading-tight">
                    Kenya Data Protection Act (2019) Privacy Notice
                </h3>
            </div>
            <button type="button"
                    onclick="closeKdpaModal()"
                    aria-label="Close modal"
                    class="p-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Scrollable Legal Summary -->
        <div class="p-5 sm:p-6 overflow-y-auto space-y-4 text-xs text-slate-600 leading-relaxed">
            <p>
                Pursuant to <strong>Article 31(c) & (d) of the Constitution of Kenya</strong> and the <strong>Data Protection Act, No. 24 of 2019</strong> (commenced 25 November 2019), <strong>Strathmore University (People & Culture)</strong> acts as the Data Controller for visitor information collected at this wellness event.
            </p>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2">
                <h4 class="font-extrabold text-[#02338d] text-xs uppercase tracking-wider">1. Purpose & Data Minimization (Sections 25 & 29)</h4>
                <p>
                    We collect only your <strong>Full Name, Email Address, Phone Number, Visitor Category, Organization/Host</strong>, and check-in timestamp strictly for: (a) verifying visitor identity via OTP, (b) venue attendance & room capacity tracking, and (c) campus emergency safety records. Your details are <strong>never used for direct marketing</strong> (Section 37) or shared with unauthorized third parties.
                </p>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2">
                <h4 class="font-extrabold text-[#02338d] text-xs uppercase tracking-wider">2. Your Rights as a Data Subject (Sections 26 & 40)</h4>
                <ul class="list-disc pl-4 space-y-1">
                    <li><strong>Right to be informed</strong> of the exact use of your personal data before collection.</li>
                    <li><strong>Right of access</strong> to any personal data held about you by Strathmore University.</li>
                    <li><strong>Right to object</strong> to the processing of all or part of your personal data.</li>
                    <li><strong>Right to rectification & erasure</strong> of false, misleading, or unnecessary personal data.</li>
                    <li><strong>Right to withdraw consent</strong> at any time under <strong>Section 32</strong> (without affecting prior lawful processing).</li>
                </ul>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-2">
                <h4 class="font-extrabold text-[#02338d] text-xs uppercase tracking-wider">3. Retention & Security (Sections 39 & 41)</h4>
                <p>
                    Visitor attendance logs are protected against unauthorized access and retained for no longer than <strong>1 academic year</strong> for institutional safety and compliance auditing, after which personal identifiers are securely purged or anonymized.
                </p>
            </div>

            <div class="bg-[#e8f0fe]/60 border border-[#02338d]/20 rounded-2xl p-4 space-y-2">
                <h4 class="font-extrabold text-[#02338d] text-xs uppercase tracking-wider">4. Read the Full Statute & Contact</h4>
                <p>
                    To exercise your data rights or contact our Data Protection Office, email
                    <a href="mailto:systems@strathmore.edu" class="font-extrabold text-[#02338d] underline">systems@strathmore.edu</a>.
                </p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <a href="https://www.odpc.go.ke/wp-content/uploads/2024/02/TheDataProtectionAct__No24of2019.pdf"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 bg-[#02338d] hover:bg-[#01225e] text-white font-extrabold text-[11px] px-3.5 py-2 rounded-xl transition">
                        <span>Download Official Data Protection Act 2019 (PDF) ↗</span>
                    </a>
                    <a href="https://www.odpc.go.ke/data-protection-laws-kenya/"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 font-extrabold text-[11px] px-3.5 py-2 rounded-xl transition">
                        <span>ODPC Kenya Laws Portal ↗</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 sm:px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex justify-end shrink-0">
            <button type="button"
                    onclick="closeKdpaModal()"
                    class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-5 py-2.5 rounded-xl shadow-sm transition">
                I Understand • Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchCheckInTab(tab) {
        const staffPanel = document.getElementById('panel-staff');
        const guestPanel = document.getElementById('panel-guest');
        const staffBtn   = document.getElementById('tab-btn-staff');
        const guestBtn   = document.getElementById('tab-btn-guest');
        if (!staffPanel || !guestPanel || !staffBtn || !guestBtn) return;

        const isStaff = tab === 'staff';
        staffPanel.classList.toggle('hidden', !isStaff);
        guestPanel.classList.toggle('hidden', isStaff);

        staffBtn.classList.toggle('bg-[#02338d]', isStaff);
        staffBtn.classList.toggle('text-white', isStaff);
        staffBtn.classList.toggle('shadow-sm', isStaff);
        staffBtn.classList.toggle('text-slate-600', !isStaff);

        guestBtn.classList.toggle('bg-[#02338d]', !isStaff);
        guestBtn.classList.toggle('text-white', !isStaff);
        guestBtn.classList.toggle('shadow-sm', !isStaff);
        guestBtn.classList.toggle('text-slate-600', isStaff);
    }

    function openKdpaModal() {
        const modal = document.getElementById('kdpa-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeKdpaModal() {
        const modal = document.getElementById('kdpa-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeKdpaModal();
    });

    // Live OTP Expiry & 60-second Resend Cooldown Timers
    document.addEventListener('DOMContentLoaded', function () {
        const expEl = document.getElementById('otp-expiry-countdown');
        if (expEl && expEl.dataset.expiresAt) {
            const target = new Date(expEl.dataset.expiresAt).getTime();
            const tickExpiry = () => {
                const diff = target - Date.now();
                if (diff <= 0) {
                    expEl.textContent = 'Code expired — please request a new verification code.';
                    expEl.classList.add('text-rose-600');
                    return;
                }
                const mins = Math.floor(diff / 60000);
                const secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
                expEl.textContent = `Code expires in ${mins}:${secs}`;
            };
            tickExpiry();
            setInterval(tickExpiry, 1000);
        }

        const resendBtn = document.getElementById('resend-otp-btn');
        if (resendBtn) {
            let wait = parseInt(resendBtn.dataset.wait || '0', 10);
            if (wait > 0) {
                const timer = setInterval(() => {
                    wait -= 1;
                    if (wait <= 0) {
                        clearInterval(timer);
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Resend Verification Code';
                    } else {
                        resendBtn.textContent = `Resend code in ${wait}s`;
                    }
                }, 1000);
            }
        }
    });
</script>
@endpush