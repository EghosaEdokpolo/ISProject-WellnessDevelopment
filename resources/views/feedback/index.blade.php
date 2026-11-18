@extends('layouts.app')

@section('content')
@php
    $tz = (string) config('wellloop.timezone', 'Africa/Nairobi');
@endphp

<div class="max-w-7xl mx-auto">

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-extrabold border border-emerald-200 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-8">
        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
            WHO-5 Wellbeing Impact
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-2">My Wellness Journey</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Review your attended sessions, track your WHO-5 wellbeing lift, and claim your post-event points.</p>
    </div>

    @if($registrations->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-12 text-center flex flex-col items-center justify-center mt-8">
            <div class="w-16 h-16 bg-[#e8f0fe] text-[#02338d] rounded-full flex items-center justify-center mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-lg font-extrabold text-slate-900 mb-1">No feedback history yet</h3>
            <p class="text-slate-500 text-xs max-w-md mx-auto mb-6">
                Once you check in at an event venue via QR poster, your session will appear here so you can submit your Post-Event WHO-5 reflection.
            </p>
            <a href="{{ route('events.index') }}" class="bg-[#02338d] hover:bg-[#012466] text-white text-xs font-extrabold py-3 px-6 rounded-xl transition shadow-sm">
                Browse Upcoming Events →
            </a>
        </div>
    @else
        <!-- SUMMARY DASHBOARD -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2.5 bg-[#e8f0fe] text-[#02338d] rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-slate-500 text-xs font-extrabold uppercase tracking-wider">Events Attended</h3>
                </div>
                <div class="text-3xl font-extrabold text-slate-900">{{ $totalAttended }}</div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-slate-500 text-xs font-extrabold uppercase tracking-wider">Feedback Submitted</h3>
                </div>
                <div class="text-3xl font-extrabold text-emerald-600">{{ $totalCompleted }}</div>
            </div>

            <div class="bg-amber-50/70 rounded-3xl p-6 border border-amber-200 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2.5 bg-amber-100 text-amber-800 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-amber-900 text-xs font-extrabold uppercase tracking-wider">Action Required</h3>
                </div>
                <div class="text-3xl font-extrabold text-amber-800">{{ $pendingCount }}</div>
                <p class="text-xs font-bold text-amber-700 mt-1">
                    {{ $pendingCount > 0 ? 'Complete pending Post-WHO surveys to earn points!' : 'All post-event surveys completed!' }}
                </p>
            </div>
        </div>

        <!-- FEEDBACK LIST -->
        <h2 class="text-lg font-extrabold text-slate-900 mb-5">Event History</h2>

        <div class="grid gap-5">
            @foreach($registrations as $reg)
                @php
                    $pre          = $reg->wellbeingResponses->whereIn('stage', ['pre', 'baseline'])->first();
                    $post         = $reg->wellbeingResponses->firstWhere('stage', 'post');
                    $lift         = ($pre && $post) ? ((int) $post->total_score - (int) $pre->total_score) : null;
                    $isCompleted  = $reg->status === 'completed' || $reg->feedback !== null;
                    $stars        = (int) ($reg->feedback?->satisfaction_stars ?? 5);
                    $evStart      = $reg->event->starts_at->copy()->shiftTimezone($tz);
                @endphp

                <div class="bg-white rounded-3xl border {{ ! $isCompleted ? 'border-amber-300 shadow-md' : 'border-slate-200 shadow-sm' }} p-6 flex flex-col md:flex-row items-center justify-between gap-6">

                    <!-- Event Info -->
                    <div class="flex-1 w-full">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                                {{ strtoupper($reg->event->category) }}
                            </span>
                            <span class="text-xs font-semibold text-slate-500">
                                {{ $evStart->format('l, d M Y') }}
                            </span>
                        </div>
                        <h3 class="font-extrabold text-lg text-slate-900">{{ $reg->event->name }}</h3>
                        <p class="text-xs text-slate-500 italic mt-0.5">{{ $reg->event->location }}</p>
                    </div>

                    <!-- Status / Action Area -->
                    <div class="shrink-0 w-full md:w-auto">
                        @if($isCompleted)
                            <div class="bg-emerald-50 rounded-2xl p-4 text-center border border-emerald-200 min-w-[210px]">
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider mb-1.5">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Feedback Submitted
                                </span>
                                <div class="flex justify-center gap-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $stars ? 'text-[#c99a2e]' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                </div>
                                @if($lift !== null)
                                    <span class="inline-block bg-emerald-100 text-emerald-800 font-extrabold text-[11px] px-2.5 py-0.5 rounded-lg">
                                        {{ $lift >= 0 ? '+' . $lift : $lift }} WHO-5 Lift
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="bg-amber-50 rounded-2xl p-4 text-center border border-amber-200 min-w-[210px]">
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-amber-700 uppercase tracking-wider mb-1.5">
                                    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Pending
                                </span>
                                <p class="text-xs text-amber-800 mb-3 font-medium">
                                    Complete your survey to earn <strong>+{{ $reg->event->points_feedback ?? 10 }} pts</strong>
                                </p>
                                <a href="{{ route('feedback.create', $reg) }}" class="block w-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-extrabold py-2.5 rounded-xl transition shadow-sm">
                                    Submit Now
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection