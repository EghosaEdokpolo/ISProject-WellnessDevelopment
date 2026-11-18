@extends('layouts.app')

@section('content')
@php
    $isHr               = $user->isHrCoordinator();
    $tz                 = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $opensBeforeMinutes = (int) config('wellloop.checkin.opens_minutes_before', 60);
    $lateGraceMinutes   = (int) config('wellloop.checkin.late_grace_minutes', 10);
    $penaltyPercent     = (int) config('wellloop.checkin.late_penalty_percent', 40);
    $now                = now($tz);
@endphp

<div class="max-w-7xl mx-auto space-y-10">

    @if(session('confirmed'))
        @php $c = session('confirmed'); @endphp
        <div class="bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold border border-emerald-200 flex items-center justify-between gap-3 shadow-sm">
            <span>
                {{ $c['already_checked_in'] ? 'Already checked in:' : 'Checked in:' }}
                <strong>{{ $c['name'] }}</strong> for <strong>{{ $c['event'] }}</strong>
                @if(($c['points'] ?? 0) > 0)
                    (+{{ $c['points'] }} pts)
                @endif
            </span>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-sm font-bold border border-rose-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('error') ?? $errors->first() }}</span>
        </div>
    @endif

    <!-- Section 1: Pending Registrations -->
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-2.5">
                <span class="w-1.5 h-6 bg-[#02338d] rounded-full"></span>
                <h1 class="text-xl font-extrabold text-slate-900">Your Registered Events</h1>
            </div>
            <span class="text-xs font-bold text-[#02338d] bg-[#e8f0fe] px-3.5 py-1.5 rounded-full">
                Gate unlocks 1 hr before start • Closes 10 mins after start
            </span>
        </div>

        @if($activeRegistrations->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200 p-8 text-center shadow-sm">
                <p class="text-sm font-bold text-slate-700">No pending event check-ins.</p>
                <p class="text-xs text-slate-500 mt-1 mb-4">
                    Browse upcoming sessions or scan the printed QR poster at any event venue once the check-in gate opens.
                </p>
                <a href="{{ route('events.index') }}" class="inline-block bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-5 py-3 rounded-xl transition">
                    Browse Upcoming Events →
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeRegistrations as $reg)
                    @php
                        $ev           = $reg->event;
                        $eventStart   = $ev->starts_at->copy()->shiftTimezone($tz);
                        $gateOpensAt  = $eventStart->copy()->subMinutes($opensBeforeMinutes);
                        $gateClosesAt = $eventStart->copy()->addMinutes($lateGraceMinutes);
                        $isGateOpen   = $now->between($gateOpensAt, $gateClosesAt);
                        $isLateGrace  = $isGateOpen && $now->gt($eventStart);
                        $isTooEarly   = $now->lt($gateOpensAt);
                        $basePts      = (int) ($ev->points_checkin ?? 25);
                        $latePts      = max(1, (int) round($basePts * ((100 - $penaltyPercent) / 100)));
                    @endphp
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                        <div class="h-1.5 {{ $isLateGrace ? 'bg-amber-500' : ($isGateOpen ? 'bg-emerald-500' : 'bg-[#02338d]') }}"></div>
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    @if($isLateGrace)
                                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                                            Late Grace • Closes {{ $gateClosesAt->format('H:i') }}
                                        </span>
                                    @elseif($isGateOpen)
                                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Gate Open • Ready
                                        </span>
                                    @elseif($reg->waiver)
                                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Waiver Signed
                                        </span>
                                    @else
                                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                                            Waiver Pending
                                        </span>
                                    @endif

                                    @if($isLateGrace)
                                        <span class="text-[11px] font-extrabold text-amber-800 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                            +{{ $latePts }} pts (-{{ $penaltyPercent }}%)
                                        </span>
                                    @else
                                        <span class="text-[11px] font-extrabold text-[#c99a2e] bg-amber-50 px-2.5 py-0.5 rounded-full">
                                            +{{ $basePts }} pts
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-lg font-extrabold text-slate-900">{{ $ev->name }}</h2>
                                <p class="text-xs text-slate-500 italic mt-1">
                                    {{ $eventStart->format('l, d M Y \a\t H:i') }} EAT<br>
                                    {{ $ev->location }}
                                </p>
                            </div>

                            <div class="mt-6 space-y-2.5">
                                @if(! $reg->waiver)
                                    <a href="{{ route('registrations.waiver', $reg) }}"
                                       class="w-full block text-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                                        Sign Mandatory Waiver First →
                                    </a>
                                @elseif($isLateGrace)
                                    <a href="{{ route('registrations.ticket', $reg) }}"
                                       class="w-full block text-center bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                                        Late Grace Active — Scan Venue QR (+{{ $latePts }} pts) →
                                    </a>
                                @elseif($isGateOpen)
                                    <a href="{{ route('registrations.ticket', $reg) }}"
                                       class="w-full block text-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                                        Open Event Pass & Scan Venue QR →
                                    </a>
                                @elseif($isTooEarly)
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-center text-[11px] text-slate-600 font-bold">
                                        Gate opens {{ $gateOpensAt->format('D, d M \a\t H:i') }} EAT (1 hr before start)
                                    </div>
                                    <a href="{{ route('registrations.ticket', $reg) }}"
                                       class="w-full block text-center bg-[#e8f0fe] hover:bg-blue-100 text-[#02338d] text-xs font-extrabold py-3 rounded-xl transition">
                                        View Event Pass & Countdown
                                    </a>
                                @else
                                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-center text-[11px] text-rose-700 font-bold">
                                        Check-in gate closed at {{ $gateClosesAt->format('H:i') }} EAT
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Section 2: Verified Check-Ins -->
    @if($pastCheckIns->isNotEmpty())
        <div>
            <div class="flex items-center gap-2.5 mb-5">
                <span class="w-1.5 h-6 bg-emerald-600 rounded-full"></span>
                <h2 class="text-lg font-extrabold text-slate-900">Verified Attendance ({{ $totalCheckIns }})</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pastCheckIns as $reg)
                    @php
                        $ev                = $reg->event;
                        $feedbackSubmitted = $reg->status === 'completed' || $reg->feedback !== null;
                    @endphp
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Checked In ✓
                                </span>
                                <span class="text-[11px] font-bold text-slate-400">
                                    {{ optional($reg->checkIn->checked_in_at)?->copy()->setTimezone($tz)->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900">{{ $ev->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ $ev->location }}</p>
                        </div>

                        <div class="mt-5">
                            @if($feedbackSubmitted)
                                <div class="w-full flex items-center justify-center gap-2 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-extrabold py-3 rounded-xl">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Post-Event Feedback Submitted</span>
                                </div>
                            @else
                                <a href="{{ route('feedback.create', $reg) }}"
                                   class="w-full block text-center bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3 rounded-xl transition">
                                    Complete Post-Event Feedback →
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Section 3: HR Coordinator Controls Only -->
    @if($isHr)
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-6 border-t border-slate-200">

            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <h2 class="text-lg font-extrabold text-slate-900">Staff ID Fallback</h2>
                    <span class="text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">HR Only</span>
                </div>
                <p class="text-xs text-slate-500 mb-5">Manual override if a staff member cannot scan the printed QR poster.</p>

                <form method="POST" action="{{ route('checkin.manual') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Select Event</label>
                        <select name="event_id" required class="w-full border border-slate-200 rounded-xl text-xs font-bold p-3 bg-slate-50 focus:ring-[#02338d] focus:border-[#02338d]">
                            @foreach($upcomingEvents as $ev)
                                <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Staff ID</label>
                        <input type="text"
                               name="staff_id"
                               value="{{ old('staff_id') }}"
                               placeholder="e.g., 000023"
                               required
                               class="w-full border border-slate-200 rounded-xl text-sm font-bold p-3 bg-slate-50 focus:ring-[#02338d] focus:border-[#02338d]">
                    </div>

                    <button type="submit" class="w-full bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                        Verify & Check In
                    </button>
                </form>
            </div>

            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-extrabold text-slate-900">Printable Venue QR Posters</h2>
                <p class="text-xs text-slate-500 mt-0.5 mb-6">
                    Print the QR poster for the venue door—gate opens 1 hour before start and closes 10 minutes after start.
                </p>

                <div class="divide-y divide-slate-100">
                    @forelse($upcomingEvents as $ev)
                        @php $evStart = $ev->starts_at->copy()->shiftTimezone($tz); @endphp
                        <div class="py-4 first:pt-0 last:pb-0 flex flex-wrap sm:flex-nowrap items-center justify-between gap-4">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900">{{ $ev->name }}</h3>
                                <p class="text-xs text-slate-500 italic mt-0.5">
                                    {{ $evStart->format('l, d M Y \a\t H:i') }} EAT • {{ $ev->location }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('events.qr.poster', $ev) }}"
                                   target="_blank"
                                   class="text-xs font-extrabold bg-[#c99a2e] hover:bg-yellow-500 text-[#01225e] px-4 py-2.5 rounded-xl shadow-sm transition">
                                    Print QR Poster
                                </a>

                                <a href="{{ route('checkin.scan', $ev) }}"
                                   class="text-xs font-extrabold bg-[#e8f0fe] hover:bg-blue-100 text-[#02338d] px-4 py-2.5 rounded-xl transition">
                                    Live Monitor →
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 py-4">No upcoming events available.</p>
                    @endforelse
                </div>
            </div>

        </div>
    @endif

</div>
@endsection