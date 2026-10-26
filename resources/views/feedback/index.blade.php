@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">My Feedback</h1>
        <p class="text-sm text-gray-500 mt-1">Submit feedback for events you've attended to earn points and help us improve.</p>
    </div>

    <div class="grid gap-4 max-w-3xl">
        @forelse($registrations as $reg)
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">{{ $reg->event->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $reg->event->starts_at->format('l, d M Y') }} · {{ $reg->event->location }}</p>
                    
                    @if($reg->status === 'completed')
                        <span class="inline-flex items-center gap-1 mt-3 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Feedback Submitted
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 mt-3 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-amber-100 text-amber-700">
                            Pending Feedback
                        </span>
                    @endif
                </div>
                
                @if($reg->status !== 'completed')
                    <a href="{{ route('feedback.create', $reg) }}" class="bg-wl-indigo text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-indigo-700 transition shadow-sm">
                        Submit Feedback
                    </a>
                @else
                    <a href="{{ route('rewards.index') }}" class="text-wl-indigo font-semibold text-sm hover:underline">
                        View Points &rarr;
                    </a>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl p-12 text-center text-gray-500 border border-gray-100">
                <div class="text-4xl mb-4">📭</div>
                You haven't attended any events yet.<br>
                Register for an event and check in to leave feedback!
            </div>
        @endforelse
    </div>
@endsection