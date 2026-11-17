@extends('layouts.app')

@section('content')
@php
    $tz                 = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $opensBeforeMinutes = (int) config('wellloop.checkin.opens_minutes_before', 60);
    $closesAfterHours   = (int) config('wellloop.checkin.closes_hours_after', 8);

    $eventStart   = $event->starts_at->copy()->shiftTimezone($tz);
    $gateOpensAt  = $eventStart->copy()->subMinutes($opensBeforeMinutes);
    $gateClosesAt = $eventStart->copy()->addHours($closesAfterHours);
    $now          = now($tz);

    $isCheckedIn = $registration->checkIn !== null || in_array($registration->status, ['checked_in', 'completed'], true);
    $isGateOpen  = ! $isCheckedIn && $now->between($gateOpensAt, $gateClosesAt);
    $isTooEarly  = ! $isCheckedIn && $now->lt($gateOpensAt);
    $isClosed    = ! $isCheckedIn && $now->gt($gateClosesAt);

    $passCode = 'WL-' . str_pad((string) $registration->id, 5, '0', STR_PAD_LEFT);
@endphp

<div class="max-w-md mx-auto px-4 py-8">

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('events.index') }}" class="text-xs font-extrabold text-slate-600 hover:text-[#02338d] transition">
            ← Back to Events
        </a>
        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
            Gate Opens 1 Hr Before Start
        </span>
    </div>

    <!-- Official Event Pass Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden">
        <!-- Navy Header -->
        <div class="bg-[#02338d] text-white px-6 py-7 text-center">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200 block">
                Strathmore University WellLoop
            </span>
            <h1 class="text-2xl font-extrabold tracking-tight mt-1">Official Event Pass</h1>
        </div>

        <!-- Body -->
        <div class="p-6 sm:p-8">
            <div class="text-center pb-6 border-b border-dashed border-slate-200">
                <h2 class="text-xl font-extrabold text-slate-900">{{ $event->name }}</h2>
                <p class="text-xs text-slate-500 italic mt-1.5">
                    {{ $eventStart->format('l, d M Y \a\t H:i') }} EAT<br>
                    {{ $event->location }}
                </p>
            </div>

            <!-- Ticket Details -->
            <div class="py-6 space-y-3.5 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 italic">Attendee</span>
                    <span class="font-extrabold text-slate-900">{{ $attendee->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 italic">Staff ID</span>
                    <span class="font-extrabold text-slate-900">{{ $attendee->staff_id }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 italic">Pass Code</span>
                    <span class="font-extrabold text-[#02338d]">{{ $passCode }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 italic">Gate Window</span>
                    <span class="font-extrabold text-slate-700 text-xs">
                        {{ $gateOpensAt->format('H:i') }} – {{ $gateClosesAt->format('H:i') }} EAT
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 italic">Status</span>
                    @if($isCheckedIn)
                        <span class="font-extrabold text-emerald-600">Checked In ✓</span>
                    @elseif($isGateOpen)
                        <span class="font-extrabold text-emerald-600 inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Gate Open
                        </span>
                    @elseif($isTooEarly)
                        <span class="font-extrabold text-amber-600">Registered • Gate Locked</span>
                    @else
                        <span class="font-extrabold text-rose-600">Check-In Closed</span>
                    @endif
                </div>
            </div>

            <!-- Gate-Controlled Action Section -->
            @if($isCheckedIn)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center mb-4">
                    <p class="text-xs font-extrabold text-emerald-800">
                        Verified at {{ optional($registration->checkIn?->checked_in_at)?->copy()->setTimezone($tz)->format('H:i \o\n d M Y') }} EAT
                    </p>
                </div>
                <a href="{{ route('feedback.create', $registration) }}"
                   class="w-full block text-center bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                    {{ $registration->status === 'completed' ? 'View Post-Event Feedback' : 'Proceed to Post-Event Feedback →' }}
                </a>

            @elseif($isGateOpen)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center mb-4">
                    <p class="text-xs font-extrabold text-emerald-900">Venue Check-In Gate is Now Open!</p>
                    <p class="text-[11px] text-emerald-700 mt-0.5">
                        Scan the printed QR poster at {{ $event->location }} to record your arrival (+{{ $event->points_checkin ?? 20 }} pts).
                    </p>
                </div>

                <button type="button"
                        onclick="openPassQrScanner()"
                        class="w-full bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    Scan Venue Entrance QR Poster
                </button>

                <div id="pass-qr-scanner-box" class="hidden mt-4 border-2 border-[#02338d] rounded-2xl overflow-hidden bg-slate-900 p-3">
                    <div id="pass-qr-reader" class="w-full rounded-xl overflow-hidden"></div>
                    <p id="pass-qr-status" class="text-[11px] text-center text-white font-bold mt-2">
                        Point your camera at the HR-printed QR poster at the venue door...
                    </p>
                </div>

            @elseif($isTooEarly)
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-center mb-4">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full">
                        Check-In Not Yet Open
                    </span>
                    <p class="text-xs font-extrabold text-slate-800 mt-2">
                        Gate unlocks {{ $gateOpensAt->format('D, d M Y \a\t H:i') }} EAT
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1" id="gate-countdown" data-opens-at="{{ $gateOpensAt->toIso8601String() }}">
                        Calculating time remaining...
                    </p>
                </div>

                <button type="button"
                        disabled
                        class="w-full bg-slate-200 text-slate-500 text-xs font-extrabold py-3.5 rounded-xl cursor-not-allowed">
                    Check-In Opens at {{ $gateOpensAt->format('H:i') }} (1 Hr Before Start)
                </button>

            @else
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 text-center">
                    <p class="text-xs font-extrabold text-rose-800">Event Check-In Window Has Closed</p>
                    <p class="text-[11px] text-rose-600 mt-1">
                        Contact People & Culture if you attended this session and need manual verification.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($isTooEarly)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('gate-countdown');
        if (!el) return;

        const targetTime = new Date(el.dataset.opensAt).getTime();

        function tick() {
            const diff = targetTime - Date.now();
            if (diff <= 0) {
                window.location.reload();
                return;
            }

            const days    = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours   = Math.floor((diff / (1000 * 60 * 60)) % 24);
            const minutes = Math.floor((diff / (1000 * 60)) % 60);
            const seconds = Math.floor((diff / 1000) % 60);

            const parts = [];
            if (days > 0) parts.push(`${days}d`);
            parts.push(`${hours}h`, `${minutes}m`, `${seconds}s`);

            el.innerText = `Unlocks automatically in ${parts.join(' ')}`;
        }

        tick();
        setInterval(tick, 1000);
    });
</script>
@endif

@if($isGateOpen)
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let scannerStarted = false;

    function openPassQrScanner() {
        const box = document.getElementById('pass-qr-scanner-box');
        const statusEl = document.getElementById('pass-qr-status');
        box.classList.remove('hidden');

        if (scannerStarted) return;
        scannerStarted = true;

        const html5QrCode = new Html5Qrcode('pass-qr-reader');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 220, height: 220 } },
            function (decodedText) {
                if (decodedText.includes('/checkin/event/')) {
                    statusEl.innerText = 'QR Code Verified! Checking you in...';
                    html5QrCode.stop().finally(() => {
                        window.location.href = decodedText;
                    });
                } else {
                    statusEl.innerText = 'Invalid QR code. Please scan the official Strathmore WellLoop poster.';
                }
            }
        ).catch(function () {
            statusEl.innerText = 'Camera blocked in browser — open your phone Camera app and scan the printed venue poster directly.';
        });
    }
</script>
@endif
@endpush