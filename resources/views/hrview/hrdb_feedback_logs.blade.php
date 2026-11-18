@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap">
<style>
    .su-brand-scope, .su-brand-scope * {
        font-family: 'Montserrat', sans-serif;
    }
</style>

<div class="su-brand-scope max-w-7xl mx-auto space-y-6">
    <x-hr-nav />

    <!-- Header & Back Link -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('dashboard.hr') }}" class="text-xs font-extrabold text-[#02338d] hover:underline">
                ← Back to HR Overview
            </a>
            <h1 class="text-2xl font-extrabold text-[#1d1d1b] mt-1 tracking-tight">Qualitative Post-Event Feedback Logs</h1>
            <p class="text-xs text-[#1d1d1b]/60 mt-0.5">
                Complete archive of post-session reflections, star ratings, and staff wellness feedback.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="bg-white border border-[#1d1d1b]/10 rounded-2xl px-4 py-2.5 text-center shadow-sm">
                <span class="block text-[10px] font-extrabold uppercase text-[#1d1d1b]/50">Total Logs</span>
                <span class="text-base font-extrabold text-[#1d1d1b]">{{ $totalCount }}</span>
            </div>
            <div class="bg-[#f5eccd]/50 border border-[#c99a2e]/35 rounded-2xl px-4 py-2.5 text-center shadow-sm">
                <span class="block text-[10px] font-extrabold uppercase text-[#8a6514]">Avg Rating</span>
                <span class="text-base font-extrabold text-[#8a6514]">{{ number_format($avgRating, 1) }} ★</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('hr.feedback.logs') }}" class="bg-white rounded-3xl border border-[#1d1d1b]/10 p-5 shadow-sm grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-5">
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#1d1d1b]/70 mb-1">Filter by Wellness Session</label>
            <select name="event_id" class="w-full border border-[#1d1d1b]/15 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-[#1d1d1b]/[0.02] focus:ring-[#02338d] focus:border-[#02338d]">
                <option value="">All Wellness Events</option>
                @foreach($events as $ev)
                    <option value="{{ $ev->id }}" {{ $selectedEventId === $ev->id ? 'selected' : '' }}>
                        {{ $ev->name }} ({{ $ev->starts_at->copy()->shiftTimezone($tz)->format('d M Y') }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-5">
            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-[#1d1d1b]/70 mb-1">Search Comment, Staff, or Department</label>
            <input type="text"
                   name="search"
                   value="{{ $search }}"
                   placeholder="e.g., mindfulness, Finance, 000043..."
                   class="w-full border border-[#1d1d1b]/15 rounded-xl text-xs font-semibold text-[#1d1d1b] p-3 bg-[#1d1d1b]/[0.02] focus:ring-[#02338d] focus:border-[#02338d]">
        </div>

        <div class="md:col-span-2 flex gap-2">
            <button type="submit" class="flex-1 bg-[#02338d] hover:bg-[#012466] text-white text-xs font-extrabold py-3 rounded-xl transition">
                Filter
            </button>
            @if($selectedEventId > 0 || $search !== '')
                <a href="{{ route('hr.feedback.logs') }}" class="px-3 py-3 bg-[#e03c31]/10 hover:bg-[#e03c31]/15 text-[#b8281e] text-xs font-extrabold rounded-xl transition">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <!-- Feedback Entries List -->
    <div class="bg-white rounded-3xl border border-[#1d1d1b]/10 p-6 shadow-sm space-y-4">
        @forelse($logs as $reg)
            @php
                $fb          = $reg->feedback;
                $rating      = max(1, min(5, (int) ($fb->rating ?? 5)));
                $comment     = trim((string) ($fb->comment ?? 'No written comment provided.'));
                $submittedAt = optional($fb->created_at)?->copy()->setTimezone($tz);
            @endphp
            <div class="bg-[#1d1d1b]/[0.02] rounded-2xl p-5 border border-[#1d1d1b]/10 space-y-2.5">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider bg-[#02338d]/10 text-[#02338d] px-2.5 py-0.5 rounded-full">
                            {{ $reg->event?->name ?? 'Wellness Session' }}
                        </span>
                        @if($reg->event?->category)
                            <span class="text-[10px] font-extrabold uppercase tracking-wider bg-[#f5eccd]/80 text-[#8a6514] px-2.5 py-0.5 rounded-full ml-1">
                                {{ $reg->event->category }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-sm font-extrabold text-[#c99a2e] tracking-tight">
                            {{ str_repeat('★', $rating) }}<span class="text-[#1d1d1b]/20">{{ str_repeat('★', 5 - $rating) }}</span>
                        </span>
                        @if($submittedAt)
                            <span class="text-[11px] font-bold text-[#1d1d1b]/45">
                                {{ $submittedAt->format('d M Y, H:i') }} EAT
                            </span>
                        @endif
                    </div>
                </div>

                <p class="text-sm text-[#1d1d1b] italic leading-relaxed">
                    "{{ $comment }}"
                </p>

                <div class="text-xs text-[#1d1d1b]/60 pt-1">
                    — <span class="font-extrabold text-[#1d1d1b]">{{ $reg->user?->name ?? 'Staff Member' }}</span>
                    @if($reg->user?->staff_id)
                        <span>({{ $reg->user->staff_id }})</span>
                    @endif
                    @if($reg->user?->department)
                        <span>• {{ $reg->user->department }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-10 text-center">
                <p class="text-sm font-extrabold text-[#1d1d1b]">No post-event feedback logs match your filter.</p>
                <p class="text-xs text-[#1d1d1b]/55 mt-1">Feedback entries appear automatically after staff complete their post-session reflection.</p>
            </div>
        @endforelse

        @if($logs->hasPages())
            <div class="pt-4 border-t border-[#1d1d1b]/10">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection