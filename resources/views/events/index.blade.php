@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Upcoming Wellness Events</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">
        4 of 8 SAMHSA wellness dimensions covered in v1 (Physical, Mental, Financial, Social).
    </p>

    {{-- POINTER: each pill is just a link to the same route with a different
         ?category= value. request()->query('category') isn't set, so
         "All" is active whenever no filter is applied. Comparing
         $activeCategory (passed from the controller) against each pill's
         value drives the highlight — same pattern as the radio-pill trick
         in events/create.blade.php, but with real links instead of inputs. --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('events.index') }}"
           class="px-4 py-1.5 rounded-lg text-sm font-medium {{ !$activeCategory ? 'bg-wl-indigo text-white' : 'bg-white text-gray-600 border border-gray-200' }}">
            All
        </a>
        @foreach(['physical' => 'Physical', 'mental' => 'Mental', 'financial' => 'Financial', 'social' => 'Social'] as $value => $label)
            <a href="{{ route('events.index', ['category' => $value]) }}"
               class="px-4 py-1.5 rounded-lg text-sm font-medium {{ $activeCategory === $value ? 'bg-wl-indigo text-white' : 'bg-white text-gray-600 border border-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($activeCategory && $events->isEmpty())
        <p class="text-sm text-gray-500 bg-white rounded-lg p-4">No upcoming {{ $activeCategory }} events right now — check back soon.</p>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- POINTER: @foreach loops over the $events collection the
             EventController passed in via compact('events'). Every {{ }}
             below is Blade's escaped-output syntax — it auto-escapes HTML,
             which is what keeps user-submitted event names from being able
             to inject scripts (a basic but important security habit). --}}
        @foreach($events as $event)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="h-16 {{ $event->category_color }} flex items-end px-4 pb-2">
                    <span class="text-xs font-semibold uppercase">{{ $event->category }}</span>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900">{{ $event->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $event->starts_at->format('D, d M Y') }} · {{ $event->location }}
                    </p>

                    <div class="flex items-center justify-between mt-4">
                        <span class="text-xs {{ $event->spots_remaining <= 10 ? 'text-red-600' : 'text-gray-500' }}">
                            @if($event->spots_remaining <= 10) ⚠ @endif
                            {{ $event->spots_remaining }} spots remaining
                        </span>

                        @if(auth()->user()->isStaff())
                            <form method="POST" action="{{ route('registrations.store', $event) }}">
                                @csrf {{-- POINTER: every POST form in Laravel needs this —
                                           it's Laravel's CSRF protection token, checked
                                           automatically by middleware. Forget it and the
                                           form submission is rejected with a 419 error. --}}
                                <button class="bg-wl-gold text-wl-navy text-sm font-semibold px-4 py-1.5 rounded-md">
                                    Register
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- POINTER: a separate loop over $pastEvents (also passed from the
         controller). Same card markup, but no <form>/Register button —
         staff can look back at what they've done, they just can't
         re-register for something that already happened. --}}
    @if($pastEvents->isNotEmpty())
        <h2 class="text-lg font-bold text-gray-900 mt-10 mb-4">Past Events</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($pastEvents as $event)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden opacity-75">
                    <div class="h-16 {{ $event->category_color }} flex items-end px-4 pb-2">
                        <span class="text-xs font-semibold uppercase">{{ $event->category }}</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900">{{ $event->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $event->starts_at->format('D, d M Y') }} · {{ $event->location }}
                        </p>
                        <span class="inline-block mt-4 text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full">
                            {{ $event->registrations_count }} attended
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
