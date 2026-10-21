@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Event Check-in</h1>
        <p class="text-sm text-gray-500 mt-1">Select an event to scan the HR QR code and check in.</p>
    </div>

    <div class="grid gap-4 max-w-3xl">
        @forelse($registrations as $reg)
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
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
                    <a href="{{ route('checkin.show', $reg) }}" class="bg-wl-indigo text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-indigo-700 transition shadow-sm">
                        Scan QR Code
                    </a>
                @else
                    <a href="{{ route('feedback.create', $reg) }}" class="text-wl-indigo font-semibold text-sm hover:underline">
                        Leave Feedback &rarr;
                    </a>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl p-12 text-center text-gray-500 border border-gray-100">
                <div class="text-4xl mb-4">📷</div>
                You aren't registered for any events yet.<br>
                Go to the Events tab to register!
            </div>
        @endforelse
    </div>
@endsection