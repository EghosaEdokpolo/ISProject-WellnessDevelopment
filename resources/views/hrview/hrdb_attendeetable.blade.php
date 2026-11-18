@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full mx-auto py-2">

    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#c99a2e]">Institutional Participation Ledger</span>
            <h1 class="text-3xl font-extrabold text-[#1d1d1b] tracking-tight mt-0.5">Attendee Directory</h1>
            <p class="text-xs sm:text-sm text-[#1d1d1b]/60 mt-1">
                Live participation record including waiver consent, check-in status, and WHO-5 Well-Being scores.
            </p>
        </div>
        <a href="{{ route('reports.departmental.export') }}"
           class="bg-[#c99a2e] hover:bg-[#b58924] text-[#1d1d1b] px-5 py-2.5 rounded-xl text-xs font-extrabold shadow-sm transition flex items-center gap-2 self-start md:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export WPMIS Report
        </a>
    </div>

    <!-- Search & Live Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-[#1d1d1b]/10 shadow-sm mb-6">
        <form method="GET" action="{{ route('attendees.index') }}" class="flex flex-col md:flex-row gap-3 w-full">
            <div class="relative flex-1">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by staff name, ID, or department..."
                       class="w-full border border-[#1d1d1b]/15 rounded-xl text-xs font-semibold text-[#1d1d1b] pl-10 pr-4 py-2.5 bg-[#1d1d1b]/[0.02] focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                <svg class="w-4 h-4 text-[#1d1d1b]/40 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <select name="event_id"
                    class="border border-[#1d1d1b]/15 rounded-xl text-xs font-semibold text-[#1d1d1b] py-2.5 px-3 bg-[#1d1d1b]/[0.02] focus:ring-[#02338d] focus:border-[#02338d] min-w-[200px]"
                    onchange="this.form.submit()">
                <option value="">All Events</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ (string) request('event_id') === (string) $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>

            <select name="status"
                    class="border border-[#1d1d1b]/15 rounded-xl text-xs font-semibold text-[#1d1d1b] py-2.5 px-3 bg-[#1d1d1b]/[0.02] focus:ring-[#02338d] focus:border-[#02338d] min-w-[150px]"
                    onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Registered</option>
                <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            @if(request()->hasAny(['search', 'event_id', 'status']))
                <a href="{{ route('attendees.index') }}"
                   class="px-4 py-2.5 text-xs font-extrabold text-[#e03c31] bg-[#e03c31]/10 border border-[#e03c31]/20 rounded-xl hover:bg-[#e03c31]/15 flex items-center justify-center transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Compact Full-Width Table (No Horizontal Cutoff of Name or Status) -->
    <div class="bg-white rounded-2xl border border-[#1d1d1b]/10 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#02338d] text-white text-[10px] font-extrabold uppercase tracking-wider">
                        <th class="px-4 py-3.5">Name</th>
                        <th class="px-4 py-3.5">Event</th>
                        <th class="px-2.5 py-3.5 text-center">Attended</th>
                        <th class="px-2.5 py-3.5 text-center">Consent</th>
                        <th class="px-2.5 py-3.5 text-center">Feedback</th>
                        <th class="px-2.5 py-3.5 text-center">WHO-5 Pre</th>
                        <th class="px-2.5 py-3.5 text-center">WHO-5 Post</th>
                        <th class="px-3 py-3.5 text-center">Points</th>
                        <th class="px-4 py-3.5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1d1d1b]/10 text-xs">
                    @forelse($registrations as $reg)
                        @php
                            $pre  = $reg->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->first();
                            $post = $reg->wellbeingResponses->firstWhere('stage', 'post');
                            $lift = ($pre && $post) ? ((int) $post->total_score - (int) $pre->total_score) : null;

                            $effectiveStatus = ($reg->status === 'completed' || $reg->feedback !== null)
                                ? 'completed'
                                : (($reg->status === 'checked_in' || $reg->checkIn !== null) ? 'checked_in' : 'registered');

                            $statusClass = match($effectiveStatus) {
                                'completed'  => 'bg-[#02338d]/10 text-[#02338d] border-[#02338d]/25',
                                'checked_in' => 'bg-[#f5eccd]/90 text-[#8a6514] border-[#c99a2e]/40',
                                default      => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                            $statusLabel = str_replace('_', ' ', $effectiveStatus);
                        @endphp
                        <tr class="hover:bg-[#02338d]/[0.02] transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="font-extrabold text-[#1d1d1b]">{{ $reg->user->name }}</div>
                                <div class="text-[11px] text-[#1d1d1b]/55">{{ $reg->user->department }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-[#1d1d1b]/85 font-semibold max-w-[220px] truncate" title="{{ $reg->event->name }}">
                                {{ $reg->event->name }}
                            </td>
                            <td class="px-2.5 py-3.5 text-center">
                                @if($reg->checkIn || in_array($reg->status, ['checked_in', 'completed'], true))
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#02338d]/10 text-[#02338d]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                @else
                                    <span class="text-[#1d1d1b]/25">—</span>
                                @endif
                            </td>
                            <td class="px-2.5 py-3.5 text-center">
                                @if($reg->waiver || $reg->consent_given)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#02338d]/10 text-[#02338d]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                @else
                                    <span class="text-[#1d1d1b]/25">—</span>
                                @endif
                            </td>
                            <td class="px-2.5 py-3.5 text-center">
                                @if($reg->feedback || $reg->status === 'completed')
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#02338d]/10 text-[#02338d]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                @else
                                    <span class="text-[#1d1d1b]/25">—</span>
                                @endif
                            </td>
                            <td class="px-2.5 py-3.5 text-center font-bold text-[#1d1d1b]/80">
                                {{ $pre->total_score ?? '—' }}
                            </td>
                            <td class="px-2.5 py-3.5 text-center font-bold text-[#1d1d1b]/80">
                                {{ $post->total_score ?? '—' }}
                                @if($lift !== null && $lift > 0)
                                    <span class="text-[10px] font-extrabold text-[#02338d] block">+{{ $lift }}</span>
                                @elseif($lift !== null && $lift < 0)
                                    <span class="text-[10px] font-extrabold text-[#e03c31] block">{{ $lift }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-3.5 text-center font-extrabold text-[#02338d]">
                                {{ (int) ($reg->user->points_balance ?? 0) }}
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <h3 class="text-base font-extrabold text-[#1d1d1b]">No attendance records found</h3>
                                <p class="text-xs text-[#1d1d1b]/55 mt-1">Adjust your search criteria or event filter to view staff participation records.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="bg-[#1d1d1b]/[0.02] border-t border-[#1d1d1b]/10 px-6 py-4">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection