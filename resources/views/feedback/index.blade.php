@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Wellness Journey</h1>
        <p class="text-sm text-gray-500 mt-1">Review your past events, track your wellbeing impact, and claim your points.</p>
    </div>

    @if($registrations->isEmpty())
        <!-- EMPTY STATE: NO EVENTS ATTENDED -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-12 text-center flex flex-col items-center justify-center mt-8">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No feedback history yet</h3>
            <p class="text-gray-500 text-sm max-w-md mx-auto mb-6">You haven't attended any events that require feedback. Register for an upcoming event to start earning points and tracking your wellness impact.</p>
            <a href="{{ route('events.index') }}" class="bg-wl-indigo text-white font-bold py-2.5 px-6 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                Browse Events
            </a>
        </div>
    @else
        <!-- SUMMARY DASHBOARD -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Events Attended</h3>
                </div>
                <div class="text-4xl font-extrabold text-gray-900">{{ $totalAttended }}</div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Feedback Submitted</h3>
                </div>
                <div class="text-4xl font-extrabold text-emerald-600">{{ $totalCompleted }}</div>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-6 border border-amber-200 shadow-sm relative overflow-hidden">
                <div class="absolute -right-4 -top-4 opacity-10"><svg class="w-24 h-24 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></div>
                <div class="flex items-center gap-3 mb-2 relative z-10">
                    <div class="p-2 bg-amber-100 text-amber-700 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <h3 class="text-amber-800 text-sm font-bold uppercase tracking-wider">Action Required</h3>
                </div>
                <div class="text-4xl font-extrabold text-amber-700 relative z-10">{{ $pendingCount }}</div>
                @if($pendingCount > 0)
                    <p class="text-xs font-semibold text-amber-700 mt-2 relative z-10">Submit feedback to earn points!</p>
                @else
                    <p class="text-xs font-semibold text-amber-700 mt-2 relative z-10">You're all caught up!</p>
                @endif
            </div>
        </div>

        <!-- FEEDBACK LIST -->
        <h2 class="text-xl font-bold text-gray-900 mb-6">Event History</h2>
        
        <div class="grid gap-6">
            @foreach($registrations as $reg)
                @php
                    $pre = $reg->wellbeingResponses->firstWhere('stage', 'pre');
                    $post = $reg->wellbeingResponses->firstWhere('stage', 'post');
                    $lift = ($pre && $post) ? ($post->total_score - $pre->total_score) : 0;
                @endphp
                
                <div class="bg-white rounded-2xl border {{ $reg->status !== 'completed' ? 'border-amber-300 shadow-md' : 'border-gray-200 shadow-sm' }} p-6 flex flex-col md:flex-row items-center justify-between gap-6 hover:shadow-lg transition-all">
                    
                    <!-- Event Info -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded bg-gray-100 text-gray-600">{{ $reg->event->category }}</span>
                            <span class="text-xs font-semibold text-gray-500">{{ $reg->event->starts_at->format('l, d M Y') }}</span>
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 mb-1">{{ $reg->event->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $reg->event->location }}</p>
                    </div>

                    <!-- Status / Action Area -->
                    <div class="flex-shrink-0 w-full md:w-auto">
                        @if($reg->status === 'completed')
                            <div class="bg-emerald-50 rounded-xl p-4 text-center border border-emerald-100 min-w-[200px]">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Feedback Submitted
                                </span>
                                <div class="flex justify-center gap-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $reg->feedback->satisfaction_stars ? 'text-wl-gold' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                </div>
                                @if($lift > 0)
                                    <span class="inline-block bg-emerald-100 text-emerald-700 font-bold text-xs px-2 py-1 rounded">+{{ $lift }} WHO-5 Lift</span>
                                @endif
                            </div>
                        @else
                            <div class="bg-amber-50 rounded-xl p-4 text-center border border-amber-200 min-w-[200px]">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-2">
                                    <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Pending
                                </span>
                                <p class="text-xs text-amber-700 mb-3 font-medium">Complete your survey to earn <strong>+{{ $reg->event->points_feedback ?? 10 }} pts</strong></p>
                                <a href="{{ route('feedback.create', $reg) }}" class="block w-full bg-amber-500 text-white text-sm font-bold py-2 rounded-lg hover:bg-amber-600 transition shadow-sm">
                                    Submit Now
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection