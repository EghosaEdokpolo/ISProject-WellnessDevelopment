@extends('layouts.app')

@section('content')
@php
    $eventStart   = $event->starts_at->copy()->shiftTimezone($tz);
    $capacity     = max(1, (int) ($event->capacity ?? 80));
    $pct          = min(100, (int) round(($checkedInCount / $capacity) * 100));
    $gateStatus   = $gate['status'] ?? 'locked';
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Header Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    HR Venue Gate Monitor
                </span>

                @if($gateStatus === 'open')
                    <span class="text-[10px] font-extrabold uppercase tracking-wider bg-emerald-600 text-white px-3 py-1 rounded-full">
                        Gate Open • On-Time Window
                    </span>
                @elseif($gateStatus === 'late_grace')
                    <span class="text-[10px] font-extrabold uppercase tracking-wider bg-amber-500 text-white px-3 py-1 rounded-full">
                        Late Grace Window Active
                    </span>
                @elseif($gateStatus === 'locked')
                    <span class="text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1 rounded-full">
                        Gate Opens {{ $gate['opens_at']->format('d M, H:i') }} EAT
                    </span>
                @else
                    <span class="text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 rounded-full">
                        Gate Closed
                    </span>
                @endif

                <span class="text-xs font-bold text-slate-400">Live polling every 5s</span>
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900 mt-2">{{ $event->name }}</h1>
            <p class="text-xs text-slate-500 italic mt-0.5">
                {{ $eventStart->format('l, d M Y \a\t H:i') }} EAT • {{ $event->location }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button"
                    onclick="toggleProjectorMode()"
                    id="projector-toggle-btn"
                    class="text-xs font-extrabold bg-white border border-slate-200 hover:border-[#02338d] text-slate-700 hover:text-[#02338d] px-4 py-2.5 rounded-xl shadow-sm transition">
                Projector Kiosk Mode
            </button>

            <a href="{{ route('events.management') }}"
               class="text-xs font-extrabold bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl transition">
                ← Event Management
            </a>

            <a href="{{ route('attendees.index') }}"
               class="text-xs font-extrabold bg-[#02338d] hover:bg-[#01225e] text-white px-4 py-2.5 rounded-xl shadow-sm transition">
                Attendees Directory →
            </a>
        </div>
    </div>

    <!-- Main 2-Column HR Command Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- Left Column: Live Entrance QR Display & HR Staff ID Override -->
        <div class="lg:col-span-5 flex flex-col gap-6">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-sm text-center">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
                    Venue Entrance QR Code
                </span>
                <h2 class="text-base font-extrabold text-slate-900 mt-3">Staff Arrival Check-In</h2>
                <p class="text-xs text-slate-500 mt-1 mb-5">
                    Project or display this screen at {{ $event->location }} for instant staff self-check-in.
                </p>

                <div class="bg-slate-50 p-5 rounded-3xl border border-slate-200 inline-block mx-auto">
                    <div id="live-monitor-qr" class="bg-white p-3 rounded-2xl shadow-sm inline-block"></div>
                </div>

                <div class="mt-4 text-[11px] font-bold text-slate-500">
                    Gate Window:
                    <strong class="text-slate-800">{{ $gate['opens_at']->format('H:i') }} – {{ $gate['closes_at']->format('H:i') }} EAT</strong>
                </div>
            </div>

            <!-- HR Manual Staff ID Override -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm mt-auto">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <h3 class="text-sm font-extrabold text-slate-900">HR Manual Staff ID Check-In</h3>
                    <span class="text-[10px] font-extrabold uppercase bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                        HR Override
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-4">
                    Verify attendance manually by Staff ID if a participant cannot scan.
                </p>

                @if(session('confirmed'))
                    <div class="mb-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold px-3.5 py-2.5 rounded-xl">
                        ✓ Checked in <strong>{{ session('confirmed')['name'] }}</strong>
                    </div>
                @endif

                @if($errors->has('staff_id'))
                    <div class="mb-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold px-3.5 py-2.5 rounded-xl">
                        {{ $errors->first('staff_id') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('checkin.manual') }}" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    <input type="text"
                           name="staff_id"
                           placeholder="Staff ID (e.g. 000043)"
                           required
                           class="flex-1 border border-slate-200 rounded-xl text-xs font-bold px-3.5 py-3 bg-slate-50 focus:ring-[#02338d] focus:border-[#02338d]">
                    <button type="submit"
                            class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-5 py-3 rounded-xl transition shrink-0">
                        Check In
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Real-Time KPIs & Live Arrival Stream -->
        <div class="lg:col-span-7 flex flex-col gap-6">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <div class="grid grid-cols-3 gap-4 text-center mb-5">
                    <div class="bg-emerald-50/70 border border-emerald-200 rounded-2xl p-4">
                        <span class="text-[10px] font-extrabold uppercase text-emerald-700 block">Checked In</span>
                        <span id="live-checked-in-count" class="text-3xl font-extrabold text-emerald-900 mt-1 block">{{ $checkedInCount }}</span>
                    </div>
                    <div class="bg-blue-50/70 border border-blue-200 rounded-2xl p-4">
                        <span class="text-[10px] font-extrabold uppercase text-[#02338d] block">Registered</span>
                        <span id="live-registered-count" class="text-3xl font-extrabold text-slate-900 mt-1 block">{{ $registeredCount }}</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                        <span class="text-[10px] font-extrabold uppercase text-slate-500 block">Venue Capacity</span>
                        <span class="text-3xl font-extrabold text-slate-800 mt-1 block">{{ $event->capacity }}</span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-1.5">
                        <span>Venue Occupancy</span>
                        <span id="live-occupancy-pct">{{ $pct }}%</span>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div id="live-occupancy-bar" class="h-full bg-emerald-600 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Real-Time Arrival Feed</h3>
                        <p class="text-[11px] text-slate-400">Verified entrance scans & manual overrides</p>
                    </div>
                    <span class="text-[11px] font-extrabold text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
                        Latest 15 arrivals
                    </span>
                </div>

                <div id="live-arrivals-list" class="divide-y divide-slate-100 flex-1">
                    @forelse($recentCheckIns as $item)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-extrabold text-slate-900">
                                    {{ $item['name'] }}
                                    <span class="text-xs font-bold text-slate-400">({{ $item['staff_id'] }})</span>
                                </p>
                                <p class="text-[11px] text-slate-500">{{ $item['department'] }} • via {{ $item['method'] }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-extrabold text-slate-700 block">{{ $item['time'] }}</span>
                                @if($item['is_late'])
                                    <span class="text-[10px] font-extrabold uppercase bg-amber-50 text-amber-800 border border-amber-200 px-2 py-0.5 rounded-full">
                                        Late Grace
                                    </span>
                                @else
                                    <span class="text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full">
                                        On Time
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center py-12 text-center">
                            <p class="text-xs font-extrabold text-slate-600">No arrivals recorded yet</p>
                            <p class="text-[11px] text-slate-400 mt-1">
                                As staff scan the entrance QR code, verified check-ins will appear here automatically.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
    function toggleProjectorMode() {
        const header = document.getElementById('institutional-header');
        const btn    = document.getElementById('projector-toggle-btn');
        if (!header || !btn) return;

        const isHidden = header.classList.toggle('hidden');
        btn.innerText = isHidden ? 'Exit Kiosk Mode' : 'Projector Kiosk Mode';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('live-monitor-qr');
        if (container) {
            const canvas = document.createElement('canvas');
            container.appendChild(canvas);
            new QRious({
                element: canvas,
                value: @json($scanUrl),
                size: 230,
                level: 'H',
                background: '#ffffff',
                foreground: '#02338d'
            });
        }

        const statsUrl = @json(route('checkin.live_stats', $event));
        const capacity = {{ max(1, (int) ($event->capacity ?? 80)) }};

        async function refreshLiveStats() {
            try {
                const res = await fetch(statsUrl, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();

                document.getElementById('live-checked-in-count').innerText = data.checked_in_count;
                document.getElementById('live-registered-count').innerText = data.registered_count;

                const pct = Math.min(100, Math.round((data.checked_in_count / capacity) * 100));
                document.getElementById('live-occupancy-pct').innerText = pct + '%';
                document.getElementById('live-occupancy-bar').style.width = pct + '%';

                const listEl = document.getElementById('live-arrivals-list');
                if (data.recent_checkins && data.recent_checkins.length > 0) {
                    listEl.innerHTML = data.recent_checkins.map(item => `
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-extrabold text-slate-900">
                                    ${item.name} <span class="text-xs font-bold text-slate-400">(${item.staff_id})</span>
                                </p>
                                <p class="text-[11px] text-slate-500">${item.department} • via ${item.method}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-extrabold text-slate-700 block">${item.time}</span>
                                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full border ${item.is_late ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'}">
                                    ${item.is_late ? 'Late Grace' : 'On Time'}
                                </span>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (_) {}
        }

        setInterval(refreshLiveStats, 5000);
    });
</script>
@endpush