@extends('layouts.app')

@section('content')
<div class="max-w-7xl w-full mx-auto py-2">

    <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 sm:mb-8 gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#c99a2e]">Institutional Participation Ledger</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1d1d1b] tracking-tight mt-0.5">Attendee Directory</h1>
            <p class="text-xs sm:text-sm text-[#1d1d1b]/60 mt-1">
                Live participation record including waiver consent, check-in status, and WHO-5 Well-Being scores.
            </p>
        </div>

        <a href="{{ route('attendees.export', request()->query()) }}"
           class="w-full sm:w-auto justify-center bg-[#c99a2e] hover:bg-[#b58924] text-[#1d1d1b] px-5 py-3 sm:py-2.5 rounded-xl text-xs font-extrabold shadow-sm transition flex items-center gap-2 self-start md:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export Filtered CSV
        </a>
    </div>

    <!-- Mobile-Safe Search & Filter Form -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-[#1d1d1b]/10 shadow-sm mb-6">
        <form method="GET" action="{{ route('attendees.index') }}" id="attendee-search-form" class="flex flex-col lg:flex-row gap-3 w-full">
            <div class="flex flex-1 gap-2">
                <div class="relative flex-1 min-w-0">
                    <input type="search"
                           name="search"
                           enterkeyhint="search"
                           value="{{ request('search') }}"
                           placeholder="Search name, ID, or school..."
                           class="w-full border border-[#1d1d1b]/15 rounded-xl text-xs font-semibold text-[#1d1d1b] pl-9 pr-3 py-2.5 bg-[#1d1d1b]/[0.02] focus:bg-white focus:ring-[#02338d] focus:border-[#02338d]">
                    <svg class="w-4 h-4 text-[#1d1d1b]/40 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <button type="submit"
                        class="bg-[#02338d] hover:bg-[#01225e] text-white px-4 py-2.5 rounded-xl text-xs font-extrabold shadow-sm transition shrink-0">
                    Search
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex gap-2.5">
                <select name="event_id"
                        class="border border-[#1d1d1b]/15 rounded-xl text-xs font-semibold text-[#1d1d1b] py-2.5 px-3 bg-[#1d1d1b]/[0.02] focus:ring-[#02338d] focus:border-[#02338d] lg:min-w-[190px]"
                        onchange="this.form.submit()">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ (string) request('event_id') === (string) $event->id ? 'selected' : '' }}>
                            {{ $event->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status"
                        class="border border-[#1d1d1b]/15 rounded-xl text-xs font-semibold text-[#1d1d1b] py-2.5 px-3 bg-[#1d1d1b]/[0.02] focus:ring-[#02338d] focus:border-[#02338d] lg:min-w-[140px]"
                        onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Registered</option>
                    <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            @if(request()->hasAny(['search', 'event_id', 'status']))
                <a href="{{ route('attendees.index') }}"
                   class="px-4 py-2.5 text-xs font-extrabold text-[#e03c31] bg-[#e03c31]/10 border border-[#e03c31]/20 rounded-xl hover:bg-[#e03c31]/15 flex items-center justify-center transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Proportional Sticky First Column Table -->
    <div class="bg-white rounded-2xl border border-[#1d1d1b]/10 shadow-sm overflow-hidden">
        <div class="sm:hidden px-4 py-2.5 bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 flex items-center justify-between">
            <span>Directory Ledger</span>
            <span>← Swipe table horizontally →</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[780px] text-left border-collapse">
                <thead>
                    <tr class="bg-[#02338d] text-white text-[10px] font-extrabold uppercase tracking-wider whitespace-nowrap">
                        <th class="sticky left-0 z-20 bg-[#02338d] px-3.5 sm:px-4 py-3.5 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.3)] min-w-[130px] sm:min-w-[180px]">Name</th>
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
                        <tr class="group hover:bg-[#f6f8fc] transition-colors">
                            <td class="sticky left-0 z-10 bg-white group-hover:bg-[#f6f8fc] px-3.5 sm:px-4 py-3 max-w-[145px] sm:max-w-none border-r border-slate-100 shadow-[4px_0_8px_-4px_rgba(2,51,141,0.1)]">
                                <div class="font-extrabold text-[#1d1d1b] truncate">{{ $reg->user->name }}</div>
                                <div class="text-[10px] text-[#1d1d1b]/55 truncate">{{ $reg->user->staff_id }} · {{ $reg->user->department }}</div>
                            </td>
                            <td class="px-4 py-3 text-[#1d1d1b]/85 font-semibold max-w-[200px] truncate" title="{{ $reg->event->name }}">
                                {{ $reg->event->name }}
                            </td>
                            <td class="px-2.5 py-3 text-center">
                                @if($reg->checkIn || in_array($reg->status, ['checked_in', 'completed'], true))
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#02338d]/10 text-[#02338d]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                @else
                                    <span class="text-[#1d1d1b]/25">—</span>
                                @endif
                            </td>
                            <td class="px-2.5 py-3 text-center">
                                @if($reg->waiver || $reg->consent_given)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#02338d]/10 text-[#02338d]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                @else
                                    <span class="text-[#1d1d1b]/25">—</span>
                                @endif
                            </td>
                            <td class="px-2.5 py-3 text-center">
                                @if($reg->feedback || $reg->status === 'completed')
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#02338d]/10 text-[#02338d]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                @else
                                    <span class="text-[#1d1d1b]/25">—</span>
                                @endif
                            </td>
                            <td class="px-2.5 py-3 text-center font-bold text-[#1d1d1b]/80">
                                {{ $pre->total_score ?? '—' }}
                            </td>
                            <td class="px-2.5 py-3 text-center font-bold text-[#1d1d1b]/80">
                                {{ $post->total_score ?? '—' }}
                                @if($lift !== null && $lift > 0)
                                    <span class="text-[10px] font-extrabold text-[#02338d] block">+{{ $lift }}</span>
                                @elseif($lift !== null && $lift < 0)
                                    <span class="text-[10px] font-extrabold text-[#e03c31] block">{{ $lift }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center font-extrabold text-[#02338d]">
                                {{ (int) ($reg->user->points_balance ?? 0) }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
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
            <div class="bg-[#1d1d1b]/[0.02] border-t border-[#1d1d1b]/10 px-4 sm:px-6 py-4 overflow-x-auto">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection