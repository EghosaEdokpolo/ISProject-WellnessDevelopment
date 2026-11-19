@extends('layouts.app')

@section('content')
@php
    $eventStart = $event->starts_at ? $event->starts_at->copy()->shiftTimezone($tz) : now($tz);
@endphp

<div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

    <!-- Top Navigation & Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <a href="{{ route('events.management') }}" class="text-xs font-extrabold text-[#02338d] hover:underline">
                ← Back to All Wellness Events
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 tracking-tight">
                Session Attendance Register
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                {{ $event->name }} · {{ $eventStart->format('D, d M Y \a\t h:i A') }} EAT · {{ $event->location }}
            </p>
        </div>

        <div class="grid grid-cols-2 sm:flex items-center gap-3">
            <div class="bg-white border border-slate-200 rounded-2xl px-4 py-2.5 text-center shadow-sm">
                <span class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Checked In</span>
                <span class="text-base font-extrabold text-emerald-600">{{ $checkedInCount }}</span>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl px-4 py-2.5 text-center shadow-sm">
                <span class="block text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Registered</span>
                <span class="text-base font-extrabold text-[#02338d]">{{ $registeredCount }}</span>
            </div>
        </div>
    </div>

    @if(session('confirmed'))
        <div class="bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-extrabold border border-emerald-200 shadow-sm">
            {{ is_string(session('confirmed')) ? session('confirmed') : 'Staff check-in verified successfully.' }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 text-rose-700 px-5 py-4 rounded-2xl text-xs font-extrabold border border-rose-200 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Search Bar -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('checkin.scan', $event) }}" class="flex flex-col sm:flex-row gap-2.5">
            <div class="relative flex-1 min-w-0">
                <input type="search"
                       name="search"
                       enterkeyhint="search"
                       value="{{ $search ?? request('search') }}"
                       placeholder="Search Staff ID (e.g. 000004), Name, or Dept..."
                       class="w-full border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 pl-9 pr-3 py-2.5 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 sm:flex-none bg-[#02338d] hover:bg-[#01225e] text-white px-5 py-2.5 rounded-xl text-xs font-extrabold shadow-sm transition">
                    Search
                </button>

                @if(!empty($search))
                    <a href="{{ route('checkin.scan', $event) }}"
                       class="px-4 py-2.5 rounded-xl text-xs font-extrabold text-[#e03c31] bg-rose-50 border border-rose-200 hover:bg-rose-100 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Attendance Table (Compact Sticky Staff ID Column + Mobile Scroll) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="sm:hidden px-4 py-2.5 bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 flex items-center justify-between">
            <span>Ordered ID 1 → Last</span>
            <span>← Swipe table horizontally →</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[680px] text-left border-collapse">
                <thead>
                    <tr class="bg-[#02338d] text-white text-[10px] font-extrabold uppercase tracking-wider whitespace-nowrap">
                        <th class="sticky left-0 z-20 bg-[#02338d] px-3.5 sm:px-4 py-3.5 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.3)] min-w-[82px] sm:min-w-[115px]">Staff ID</th>
                        <th class="px-4 py-3.5">Staff Member</th>
                        <th class="px-4 py-3.5">School / Department</th>
                        <th class="px-3 py-3.5 text-center">Waiver</th>
                        <th class="px-4 py-3.5 text-center">Check-In Time</th>
                        <th class="px-4 py-3.5 text-right">Status / Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($registrations as $reg)
                        @php
                            $isCheckedIn = $reg->checkIn !== null || in_array($reg->status, ['checked_in', 'completed'], true);
                            $initial     = strtoupper(mb_substr(trim((string) $reg->user->name), 0, 1));
                        @endphp
                        <tr class="group hover:bg-[#f6f8fc] transition-colors">
                            <td class="sticky left-0 z-10 bg-white group-hover:bg-[#f6f8fc] px-3.5 sm:px-4 py-3.5 font-extrabold text-[#02338d] whitespace-nowrap border-r border-slate-100 shadow-[4px_0_8px_-4px_rgba(2,51,141,0.1)]">
                                {{ $reg->user->staff_id }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-[#e8f0fe] text-[#02338d] font-extrabold text-[11px] flex items-center justify-center overflow-hidden shrink-0">
                                        @if(!empty($reg->user->profile_photo_path))
                                            <img src="{{ asset($reg->user->profile_photo_path) }}" alt="{{ $reg->user->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ $initial }}
                                        @endif
                                    </div>
                                    <span class="font-extrabold text-slate-900">{{ $reg->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 font-semibold whitespace-nowrap">
                                {{ $reg->user->department }}
                            </td>
                            <td class="px-3 py-3.5 text-center whitespace-nowrap">
                                @if($reg->waiver || $reg->consent_given)
                                    <span class="text-emerald-600 font-extrabold">Signed ✓</span>
                                @else
                                    <span class="text-amber-600 font-bold">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center text-slate-600 font-semibold whitespace-nowrap">
                                {{ $reg->checkIn?->checked_in_at?->copy()->setTimezone($tz)->format('h:i A, d M') ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                @if($isCheckedIn)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $reg->status === 'completed' ? 'Completed ✓' : 'Checked In ✓' }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('checkin.confirm', $reg) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="bg-[#02338d] hover:bg-[#01225e] text-white text-[11px] font-extrabold px-3 py-1.5 rounded-lg transition">
                                            Verify Check-In
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 font-bold">
                                No staff attendance records match your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection