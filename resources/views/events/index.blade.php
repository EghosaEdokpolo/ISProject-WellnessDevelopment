@extends('layouts.app')

@section('content')
    <!-- Header & Filters -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Wellness Events</h1>
            <p class="text-sm text-gray-500 mt-1">Register for upcoming events or view your past history.</p>
        </div>
        
        <div class="flex gap-2 bg-white p-1.5 rounded-xl border border-gray-100 shadow-sm w-fit">
            <a href="{{ route('events.index') }}" class="px-4 py-1.5 rounded-lg text-xs font-semibold {{ !$activeCategory ? 'bg-wl-indigo text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">All</a>
            @foreach(['physical' => 'Physical', 'mental' => 'Mental', 'financial' => 'Financial', 'social' => 'Social'] as $value => $label)
                <a href="{{ route('events.index', ['category' => $value]) }}" class="px-4 py-1.5 rounded-lg text-xs font-semibold {{ $activeCategory === $value ? 'bg-wl-indigo text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-50 text-red-600 px-4 py-3 rounded-lg text-sm font-medium border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <!-- UPCOMING EVENTS SECTION -->
    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-wl-indigo" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        Upcoming Events
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        @forelse($events as $event)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                <div class="h-2 {{ $event->category_color }}"></div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full {{ $event->category_color }}">{{ $event->category }}</span>
                        <span class="text-xs font-semibold text-gray-400">{{ $event->spots_remaining }} spots left</span>
                    </div>
                    
                    <h3 class="font-bold text-lg text-gray-900 mb-2 leading-tight">{{ $event->name }}</h3>
                    <p class="text-sm text-gray-500 mb-6 line-clamp-2">{{ $event->description }}</p>

                    <div class="space-y-2 mb-6 mt-auto">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $event->starts_at->format('l, d M Y \a\t H:i') }}
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $event->location }}
                        </div>
                    </div>

                    @php
                        // OPTIMIZATION: Because we eager loaded with a constraint, we just check if the collection is empty
                        $isRegistered = $event->registrations->first();
                    @endphp

                    @if($isRegistered)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center mb-3">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">You are registered</span>
                            
                            <!-- Google Calendar Link -->
                            <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ urlencode($event->name) }}&dates={{ $event->starts_at->format('Ymd\THis') }}/{{ $event->starts_at->copy()->addHours(2)->format('Ymd\THis') }}&details={{ urlencode($event->description) }}&location={{ urlencode($event->location) }}" target="_blank" class="block w-full bg-white border border-gray-300 text-gray-700 font-semibold py-2 rounded-lg mb-2 hover:bg-gray-50 transition-colors text-sm">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Add to Calendar
                                </span>
                            </a>

                            <!-- Dynamic WhatsApp Link -->
                            @if(env('STRATHMORE_WHATSAPP_LINK'))
                                <a href="{{ env('STRATHMORE_WHATSAPP_LINK') }}" target="_blank" class="block w-full bg-[#25D366] text-white font-semibold py-2 rounded-lg hover:bg-[#128C7E] transition-colors text-sm mb-2">
                                    Join WhatsApp for Info
                                </a>
                            @endif

                            <a href="{{ route('registrations.waiver', $isRegistered) }}" class="text-sm text-wl-indigo font-bold hover:underline block mt-3">Go to Waiver & Check-in &rarr;</a>
                        </div>
                    @else
                        <a href="{{ route('registrations.create', $event) }}" class="block w-full text-center bg-wl-navy text-white text-sm font-bold py-3 rounded-xl hover:bg-gray-800 transition-colors">
                            Register for Event
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-8 text-center text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
                No upcoming events right now. Check back soon!
            </div>
        @endforelse
    </div>

    <!-- PAST EVENTS SECTION -->
    @if($pastEvents->count() > 0)
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Past Events
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-75">
            @foreach($pastEvents as $event)
                <div class="bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden flex flex-col">
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-gray-200 text-gray-600">{{ $event->category }}</span>
                            <span class="text-xs font-bold text-gray-400 uppercase">Event Ended</span>
                        </div>
                        
                        <h3 class="font-bold text-lg text-gray-700 mb-2 leading-tight">{{ $event->name }}</h3>
                        
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $event->starts_at->format('D, d M Y') }}
                        </div>

                        @php
                            $pastRegistration = $event->registrations->first();
                        @endphp

                        @if($pastRegistration)
                            @if($pastRegistration->status === 'completed')
                                <span class="inline-flex items-center gap-1 mt-auto px-3 py-1.5 text-xs font-bold rounded-lg bg-emerald-100 text-emerald-700 w-fit">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Attended & Reviewed
                                </span>
                            @elseif($pastRegistration->checkIn)
                                <a href="{{ route('feedback.create', $pastRegistration) }}" class="block w-full text-center bg-white border border-wl-indigo text-wl-indigo text-sm font-bold py-2 rounded-lg hover:bg-gray-50 mt-auto">
                                    Submit Feedback
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 mt-auto px-3 py-1.5 text-xs font-bold rounded-lg bg-red-100 text-red-600 w-fit">
                                    Missed Event
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1 mt-auto px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-200 text-gray-500 w-fit">
                                Not Registered
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection