@extends('layouts.app')

@section('content')
@php
    $tz        = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $graceMins = (int) config('wellloop.checkin.late_grace_minutes', 15);

    $eventStart   = $gate['starts_at'];
    $gateOpensAt  = $gate['opens_at'];
    $gateClosesAt = $gate['closes_at'];

    $isCheckedIn = $registration->checkIn !== null || in_array($registration->status, ['checked_in', 'completed'], true);
    $isGateOpen  = ! $isCheckedIn && $gate['is_open'];
    $isLateGrace = ! $isCheckedIn && $gate['status'] === 'late_grace';
    $isTooEarly  = ! $isCheckedIn && $gate['status'] === 'locked';

    $basePoints      = $gate['base_points'];
    $awardedPoints   = $gate['awarded_points'];
    $pointsReducedBy = $gate['points_reduced_by'];
    $penaltyPercent  = $gate['penalty_percent'];
    $minutesLate     = $gate['minutes_late'];
    $halfPoints      = $basePoints > 0 ? max(1, (int) round($basePoints * ((100 - $penaltyPercent) / 100))) : 0;

    $whatsappUrl = $event->whatsapp_channel_url;
@endphp

<div class="max-w-md mx-auto px-4 py-8">

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('events.index') }}" class="text-xs font-extrabold text-slate-600 hover:text-[#02338d] transition">
            ← Back to Events
        </a>
        <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
            Gate: -1 Hr to +{{ $graceMins }} Min
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
                    {{ $eventStart->format('l, d M Y \a\t h:i A') }} EAT<br>
                    {{ $event->location }}
                </p>
            </div>

            <!-- Ticket Details (Uses Staff ID Only) -->
            <div class="py-5 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 italic">Attendee</span>
                    <span class="font-extrabold text-slate-900">{{ $attendee->name }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400 italic">Staff ID</span>
                    <span class="font-extrabold text-[#02338d]">{{ $attendee->staff_id }}</span>
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
                    @elseif($isLateGrace)
                        <span class="font-extrabold text-amber-600 inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            Late Grace Window ({{ $minutesLate }}m late)
                        </span>
                    @elseif($isGateOpen)
                        <span class="font-extrabold text-emerald-600 inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Gate Open (On Time)
                        </span>
                    @elseif($isTooEarly)
                        <span class="font-extrabold text-amber-600">Registered • Gate Locked</span>
                    @else
                        <span class="font-extrabold text-rose-600">Check-In Closed</span>
                    @endif
                </div>
            </div>

            <!-- Event WhatsApp Channel / Group Button -->
            @if(!empty($whatsappUrl))
                <div class="mb-5">
                    <a href="{{ $whatsappUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold py-3 px-4 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Join Event WhatsApp Channel</span>
                    </a>
                </div>
            @endif

            <!-- Visual Arrival & Points Tier Gauge (1 Hr Before = 100%, +15 Min = 50%, +16 Min = 0) -->
            @if(! $isCheckedIn)
                <div class="mb-5 bg-slate-50 border border-slate-200 rounded-2xl p-4">
                    <div class="flex items-center justify-between text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                        <span>Attendance Points Schedule</span>
                        <span>{{ $graceMins }}-Min Late Allowance (50% Pts)</span>
                    </div>

                    <div class="grid grid-cols-3 gap-1.5 text-center">
                        <div class="rounded-xl p-2.5 border {{ (! $isLateGrace && $isGateOpen) || $isTooEarly ? 'bg-emerald-50 border-emerald-400 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200 opacity-60' }}">
                            <span class="block text-[9px] font-extrabold uppercase text-emerald-700">On Time (100%)</span>
                            <span class="block text-sm font-extrabold text-emerald-900 mt-0.5">+{{ $basePoints }} pts</span>
                            <span class="block text-[9px] text-slate-500 mt-0.5">{{ $gateOpensAt->format('H:i') }}–{{ $eventStart->format('H:i') }}</span>
                        </div>

                        <div class="rounded-xl p-2.5 border {{ $isLateGrace ? 'bg-amber-50 border-amber-400 ring-2 ring-amber-500/20' : 'bg-white border-slate-200 opacity-75' }}">
                            <span class="block text-[9px] font-extrabold uppercase text-amber-700">Late (50%)</span>
                            <span class="block text-sm font-extrabold text-amber-900 mt-0.5">+{{ $halfPoints }} pts</span>
                            <span class="block text-[9px] text-slate-500 mt-0.5">{{ $eventStart->format('H:i') }}–{{ $gateClosesAt->format('H:i') }}</span>
                        </div>

                        <div class="rounded-xl p-2.5 border {{ (! $isGateOpen && ! $isTooEarly) ? 'bg-rose-50 border-rose-400 ring-2 ring-rose-500/20' : 'bg-white border-slate-200 opacity-60' }}">
                            <span class="block text-[9px] font-extrabold uppercase text-rose-700">Closed</span>
                            <span class="block text-sm font-extrabold text-rose-900 mt-0.5">0 pts</span>
                            <span class="block text-[9px] text-slate-500 mt-0.5">After {{ $gateClosesAt->format('H:i') }}</span>
                        </div>
                    </div>

                    @if($isLateGrace)
                        <div class="mt-3 pt-3 border-t border-amber-200/80 flex items-center justify-between text-xs">
                            <span class="font-bold text-amber-900">
                                Half-Points Late Window (-{{ $pointsReducedBy }} pts):
                            </span>
                            <span class="font-extrabold text-amber-800">
                                <span class="line-through text-slate-400 mr-1">{{ $basePoints }} pts</span>
                                → +{{ $awardedPoints }} pts
                            </span>
                        </div>
                        <p class="text-[11px] font-bold text-rose-600 mt-1 text-center"
                           id="grace-countdown"
                           data-closes-at="{{ $gateClosesAt->copy()->endOfMinute()->toIso8601String() }}">
                            Calculating remaining grace time...
                        </p>
                    @endif
                </div>
            @endif

            <!-- Gate-Controlled Action Section -->
            @if($isCheckedIn)
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center mb-4">
                    <p class="text-xs font-extrabold text-emerald-800">
                        Verified at {{ optional($registration->checkIn?->checked_in_at)?->copy()->setTimezone($tz)->format('H:i \o\n d M Y') }} EAT
                    </p>
                </div>

                @if($registration->status === 'completed' || $registration->feedback !== null)
                    <div class="w-full text-center bg-slate-100 text-slate-700 text-xs font-extrabold py-3.5 rounded-xl border border-slate-200">
                        ✓ Post-Event Feedback Submitted
                    </div>
                @else
                    <a href="{{ route('feedback.create', $registration) }}"
                       class="w-full block text-center bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                        Complete Post-Event Feedback →
                    </a>
                @endif

            @elseif($isGateOpen)
                @if($isLateGrace)
                    <div class="bg-amber-50 border border-amber-300 rounded-2xl p-4 text-center mb-4">
                        <p class="text-xs font-extrabold text-amber-900">
                            Session Started {{ $minutesLate }} Min{{ $minutesLate > 1 ? 's' : '' }} Ago — Late Grace Active
                        </p>
                        <p class="text-[11px] text-amber-800 mt-0.5">
                            Scan the HR QR poster by <strong>{{ $gateClosesAt->format('H:i') }} EAT</strong> to earn half points (<strong>+{{ $awardedPoints }} pts</strong>). Arrivals after {{ $gateClosesAt->format('H:i') }} receive 0 pts.
                        </p>
                    </div>
                @else
                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center mb-4">
                        <p class="text-xs font-extrabold text-emerald-900">Venue Check-In Gate is Now Open!</p>
                        <p class="text-[11px] text-emerald-700 mt-0.5">
                            Scan the printed QR poster at {{ $event->location }} by {{ $eventStart->format('H:i') }} EAT for full <strong>+{{ $awardedPoints }} pts</strong>.
                        </p>
                    </div>
                @endif

                <button type="button"
                        onclick="openPassQrScanner()"
                        class="w-full bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    Scan HR Venue QR Poster
                </button>

                <div id="pass-qr-scanner-box" class="hidden mt-4 border-2 border-[#02338d] rounded-2xl overflow-hidden bg-slate-900 p-3 space-y-3">
                    <div id="pass-qr-reader" class="w-full rounded-xl overflow-hidden bg-black"></div>
                    <p id="pass-qr-status" class="text-[11px] text-center text-white font-bold">
                        Initializing HTTPS camera scanner...
                    </p>

                    <div id="pass-qr-upload-fallback" class="pt-2 border-t border-slate-700 text-center">
                        <label for="qr-file-input" class="cursor-pointer inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 text-white text-[11px] font-bold px-3.5 py-2 rounded-xl transition">
                            <span>Or Snap / Upload Photo of QR Poster</span>
                        </label>
                        <input type="file" id="qr-file-input" accept="image/*" capture="environment" class="hidden">
                    </div>
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
                    <p class="text-xs font-extrabold text-rose-800">Event Check-In Gate Has Closed</p>
                    <p class="text-[11px] text-rose-600 mt-1">
                        The {{ $graceMins }}-minute late grace window ended at {{ $gateClosesAt->format('H:i') }} EAT (0 points after cutoff). Contact People & Culture for assistance.
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

@if($isLateGrace)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('grace-countdown');
        if (!el) return;

        const closeTime = new Date(el.dataset.closesAt).getTime();

        function tickGrace() {
            const diff = closeTime - Date.now();
            if (diff <= 0) {
                window.location.reload();
                return;
            }

            const minutes = Math.floor(diff / (1000 * 60));
            const seconds = Math.floor((diff / 1000) % 60);
            el.innerText = `Gate closes in ${minutes}m ${seconds}s — scan now before entry locks!`;
        }

        tickGrace();
        setInterval(tickGrace, 1000);
    });
</script>
@endif

@if($isGateOpen)
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let scannerInstance = null;
    let scannerRunning  = false;

    function handleDecodedQr(decodedText, statusEl) {
        if (decodedText && decodedText.includes('/checkin/event/')) {
            statusEl.innerText = 'QR Verified! Entering event gate...';
            if (scannerInstance && scannerRunning) {
                scannerInstance.stop().catch(() => {}).finally(() => {
                    window.location.href = decodedText;
                });
            } else {
                window.location.href = decodedText;
            }
        } else {
            statusEl.innerText = 'Unrecognized QR code. Please scan the official HR venue poster.';
        }
    }

    async function openPassQrScanner() {
        const box      = document.getElementById('pass-qr-scanner-box');
        const statusEl = document.getElementById('pass-qr-status');
        box.classList.remove('hidden');

        if (!window.isSecureContext) {
            statusEl.innerText = 'HTTPS is required for live browser camera access. Open this page via your https:// link or snap a photo of the QR poster below.';
            return;
        }

        if (scannerRunning) return;

        if (!scannerInstance) {
            scannerInstance = new Html5Qrcode('pass-qr-reader');
        }

        const config = { fps: 12, qrbox: { width: 220, height: 220 } };
        const onSuccess = (decodedText) => handleDecodedQr(decodedText, statusEl);

        try {
            statusEl.innerText = 'Requesting camera permission...';
            await scannerInstance.start({ facingMode: 'environment' }, config, onSuccess);
            scannerRunning = true;
            statusEl.innerText = 'Point your camera at the HR-printed QR poster at the venue entrance...';
        } catch (err) {
            try {
                const cameras = await Html5Qrcode.getCameras();
                if (cameras && cameras.length > 0) {
                    const backCam = cameras.find(c => /back|rear|environment/i.test(c.label)) || cameras[cameras.length - 1];
                    await scannerInstance.start(backCam.id, config, onSuccess);
                    scannerRunning = true;
                    statusEl.innerText = 'Point your camera at the HR-printed QR poster at the venue entrance...';
                    return;
                }
            } catch (_) {}

            statusEl.innerText = 'Allow camera permission in your browser address bar, or tap "Snap / Upload Photo of QR Poster" below.';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('qr-file-input');
        const statusEl  = document.getElementById('pass-qr-status');
        if (!fileInput) return;

        fileInput.addEventListener('change', async function (e) {
            const file = e.target.files[0];
            if (!file) return;

            statusEl.innerText = 'Reading QR code from photo...';
            const fileScanner = scannerInstance || new Html5Qrcode('pass-qr-reader');

            try {
                if (scannerRunning && scannerInstance) {
                    await scannerInstance.stop();
                    scannerRunning = false;
                }
                const decodedText = await fileScanner.scanFile(file, true);
                handleDecodedQr(decodedText, statusEl);
            } catch (err) {
                statusEl.innerText = 'Could not detect a valid QR code in that photo. Please try a clearer shot.';
            }
        });
    });
</script>
@endif
@endpush