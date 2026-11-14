@extends('layouts.app')

@section('content')
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Event Check-in</h1>
            <p class="text-sm text-gray-500 mt-1">Scan the HR QR code on the day of your event to check in.</p>
        </div>
    </div>

    <div class="grid gap-4 max-w-3xl">
        @forelse($registrations as $reg)
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $reg->event->category_color }}">{{ $reg->event->category }}</span>
                        @if($reg->event->starts_at->isToday())
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-red-100 text-red-600 animate-pulse">Happening Today</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">{{ $reg->event->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $reg->event->starts_at->format('l, d M Y \a\t H:i') }} · {{ $reg->event->location }}</p>
                    
                    @if($reg->checkIn)
                        <span class="inline-flex items-center gap-1 mt-3 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-emerald-100 text-emerald-700">
                            ✓ Checked In
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 mt-3 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-amber-100 text-amber-700">
                            Pending Check-in
                        </span>
                    @endif
                </div>
                
                @if(!$reg->checkIn)
                    <div class="flex-shrink-0">
                        @if($reg->event->starts_at->isToday())
                            <a href="{{ route('checkin.show', $reg) }}" class="inline-block w-full text-center bg-wl-indigo text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-indigo-700 transition shadow-sm">
                                Scan QR Code
                            </a>
                        @else
                            <div class="text-right">
                                <button disabled class="inline-block w-full bg-gray-100 text-gray-400 px-6 py-2.5 rounded-lg font-semibold text-sm cursor-not-allowed">
                                    Check-in Opens on Event Day
                                </button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex-shrink-0">
                        <a href="{{ route('feedback.create', $reg) }}" class="inline-block text-wl-indigo font-semibold text-sm hover:underline">
                            Leave Feedback &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl p-12 text-center border border-gray-100 shadow-sm">
                <div class="text-5xl mb-4">📅</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No upcoming events</h3>
                <p class="text-gray-500 text-sm mb-6">You don't have any events to check into right now. Check the Events page for upcoming wellness activities.</p>
                <a href="{{ route('events.index') }}" class="inline-block bg-wl-navy text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-gray-800 transition">
                    Browse Events
                </a>
            </div>
        @endforelse
    </div>
@endsection