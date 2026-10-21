@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Upcoming Wellness Events</h1>
            <p class="text-sm text-gray-500 mt-1">4 of 8 SAMHSA wellness dimensions covered in v1.</p>
        </div>
    </div>

    <!-- Category Filters -->
    <div class="flex gap-2 mb-8 bg-white p-1.5 rounded-xl border border-gray-100 shadow-sm w-fit">
        <a href="{{ route('events.index') }}" class="px-5 py-2 rounded-lg text-sm font-semibold {{ !$activeCategory ? 'bg-wl-indigo text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">All</a>
        @foreach(['physical' => 'Physical', 'mental' => 'Mental', 'financial' => 'Financial', 'social' => 'Social'] as $value => $label)
            <a href="{{ route('events.index', ['category' => $value]) }}" class="px-5 py-2 rounded-lg text-sm font-semibold {{ $activeCategory === $value ? 'bg-wl-indigo text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($events as $event)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                <!-- Category Color Bar -->
                <div class="h-2 {{ $event->category_color }}"></div>
                
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full {{ $event->category_color }}">{{ $event->category }}</span>
                        <span class="text-xs font-semibold text-gray-400">{{ $event->spots_remaining }} spots left</span>
                    </div>
                    
                    <h3 class="font-bold text-lg text-gray-900 mb-2 leading-tight">{{ $event->name }}</h3>
                    <p class="text-sm text-gray-500 mb-6">{{ $event->description }}</p>

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
                        $isRegistered = $event->registrations->where('user_id', auth()->id())->first();
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
                        <form method="POST" action="{{ route('registrations.store', $event) }}">
                            @csrf
                            <button class="w-full bg-wl-navy text-white text-sm font-bold py-3 rounded-xl hover:bg-gray-800 transition-colors">
                                Register for Event
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500">
                No upcoming events right now. Check back soon!
            </div>
        @endforelse
    </div>
@endsection