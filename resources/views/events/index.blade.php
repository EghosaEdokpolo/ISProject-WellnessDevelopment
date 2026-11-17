{{-- 1. Extend the Staff Portal master layout (renders the Navy/Gold Staff bar, never <x-hr-nav />) --}}
@extends('layouts.app')

@section('content')
{{-- 2. Compute the logged-in staff member's points, appraisal tier, and existing registrations --}}
@php
    $user = auth()->user();
    $points = (int) ($user->points_balance ?? 0);
    $tier = $points >= 200 ? 'Gold' : ($points >= 100 ? 'Silver' : 'Bronze');
    $tierColors = match ($tier) {
        'Gold'   => 'bg-amber-100 text-amber-800 border-amber-300',
        'Silver' => 'bg-slate-200 text-slate-800 border-slate-300',
        default  => 'bg-orange-100 text-orange-800 border-orange-300',
    };

    $userRegistrations = $user->registrations()->get()->keyBy('event_id');
    $categories = ['all', 'physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual'];
@endphp

<div class="space-y-8">

    {{-- 3. Staff Holistic Wellness & Appraisal Standing Banner --}}
    <div class="bg-gradient-to-r from-[#02338d] to-[#01225e] rounded-3xl p-6 sm:p-8 text-white shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-widest text-[#c99a2e]">
                Strathmore University · People & Culture
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1">
                Welcome, {{ $user->name }}
            </h1>
            <p class="text-sm text-blue-100 mt-1">
                {{ $user->department ?? 'Strathmore Staff' }} • Staff ID: <span class="font-bold text-white">{{ $user->staff_id }}</span>
            </p>
        </div>

        {{-- 4. Live Points Balance, Tier Badge, and Rewards Shortcut --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="bg-white/10 border border-white/15 rounded-2xl px-4 py-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Wellness Points</p>
                <p class="text-xl font-extrabold text-white">{{ number_format($points) }} <span class="text-xs font-bold text-[#c99a2e]">pts</span></p>
            </div>

            <div class="bg-white/10 border border-white/15 rounded-2xl px-4 py-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Appraisal Standing</p>
                <span class="inline-block mt-0.5 text-xs font-extrabold px-2.5 py-0.5 rounded-full border {{ $tierColors }}">
                    {{ $tier }} Tier
                </span>
            </div>

            <a href="{{ route('rewards.index') }}" class="bg-[#c99a2e] hover:bg-yellow-500 text-[#01225e] font-extrabold text-xs px-4 py-3.5 rounded-2xl transition shadow-sm">
                View Rewards →
            </a>
        </div>
    </div>

    {{-- 5. Session Success & Validation Error Alerts --}}
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold border border-emerald-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-sm font-bold border border-rose-200 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- 6. Category Filter Strip --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Upcoming Wellness Events</h2>
            <p class="text-xs text-slate-500 mt-0.5">Register for sessions, complete your WHO-5 check-in, and earn holistic appraisal points.</p>
        </div>

        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-hide">
            @foreach($categories as $cat)
                <button type="button"
                        onclick="filterStaffEvents('{{ $cat }}', this)"
                        class="staff-cat-btn px-3 py-1.5 rounded-xl text-xs font-bold capitalize transition shrink-0 {{ $cat === 'all' ? 'bg-[#02338d] text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                    {{ $cat }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- 7. Staff Event Cards Grid --}}
    @if($events->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-14 h-14 bg-blue-50 text-[#02338d] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">No upcoming wellness events</h3>
            <p class="text-sm text-slate-500 mt-1">Check back soon for new sessions published by People & Culture.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                @php
                    $registration = $userRegistrations->get($event->id);
                    $registeredCount = $event->registrations_count ?? $event->registrations()->count();
                    $spotsLeft = max(0, (int) $event->capacity - (int) $registeredCount);
                    $totalPts = (int) $event->points_checkin + (int) $event->points_feedback;
                @endphp

                <div data-category="{{ strtolower($event->category) }}" class="staff-event-card bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300 transition flex flex-col justify-between p-6">
                    <div>
                        {{-- Category Pill & Total Earnable Points --}}
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full bg-blue-50 text-[#02338d] border border-blue-100">
                                {{ ucfirst($event->category) }}
                            </span>
                            <span class="text-[11px] font-extrabold text-[#c99a2e] bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                +{{ $totalPts }} pts
                            </span>
                        </div>

                        <h3 class="text-lg font-extrabold text-slate-900 leading-snug mb-2">
                            {{ $event->name }}
                        </h3>

                        <p class="text-xs text-slate-600 line-clamp-3 mb-4">
                            {{ $event->description }}
                        </p>

                        {{-- Date, Venue, and Remaining Capacity --}}
                        <div class="space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#02338d] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="font-bold text-slate-800">{{ $event->starts_at->format('D, d M Y \a\t H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#02338d] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>{{ $event->location }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#02338d] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span><strong class="text-slate-800">{{ $spotsLeft }}</strong> of {{ $event->capacity }} spots remaining</span>
                            </div>
                        </div>
                    </div>

                    {{-- 8. Registration State Action Button --}}
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        @if($registration)
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-2 rounded-xl">
                                    Registered ✓
                                </span>
                                <a href="{{ route('registrations.ticket', $registration) }}" class="text-xs font-extrabold bg-[#02338d] text-white px-4 py-2 rounded-xl hover:bg-[#01225e] transition">
                                    View Ticket
                                </a>
                            </div>
                        @elseif($spotsLeft > 0)
                            <a href="{{ route('registrations.create', $event) }}" class="w-full block text-center bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3 rounded-xl transition shadow-sm">
                                Register for Event →
                            </a>
                        @else
                            <button type="button" disabled class="w-full bg-slate-100 text-slate-400 text-xs font-extrabold py-3 rounded-xl cursor-not-allowed">
                                Capacity Full
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- 9. Client-side Category Filter Script --}}
<script>
    function filterStaffEvents(category, clickedBtn) {
        document.querySelectorAll('.staff-cat-btn').forEach(btn => {
            btn.classList.remove('bg-[#02338d]', 'text-white', 'shadow-sm');
            btn.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200');
        });
        clickedBtn.classList.add('bg-[#02338d]', 'text-white', 'shadow-sm');
        clickedBtn.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-200');

        document.querySelectorAll('.staff-event-card').forEach(card => {
            if (category === 'all' || card.getAttribute('data-category') === category) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }
</script>
@endsection