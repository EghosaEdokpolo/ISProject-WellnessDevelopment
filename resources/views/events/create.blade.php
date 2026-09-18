@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Manage Events</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">Create &amp; Publish Event</p>

    <div class="bg-white rounded-xl shadow-sm p-6 max-w-xl">
        <form method="POST" action="{{ route('events.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="text-sm font-medium text-gray-700">Event name</label>
                <input name="name" value="{{ old('name') }}"
                       class="w-full mt-1 border-gray-200 rounded-lg bg-gray-50 focus:ring-wl-indigo focus:border-wl-indigo">
                {{-- POINTER: $errors is automatically shared with every view
                     by Laravel after a failed validate() redirects back here.
                     No need to pass it manually from the controller. --}}
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Date &amp; time</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                           class="w-full mt-1 border-gray-200 rounded-lg bg-gray-50 focus:ring-wl-indigo focus:border-wl-indigo">
                    @error('starts_at') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Location</label>
                    <input name="location" value="{{ old('location') }}"
                           class="w-full mt-1 border-gray-200 rounded-lg bg-gray-50 focus:ring-wl-indigo focus:border-wl-indigo">
                    @error('location') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 block mb-2">Wellness category</label>
                <div class="flex gap-2">
                    {{-- POINTER: these are plain radio inputs styled to look
                         like the pill buttons in your prototype — 'peer' +
                         'peer-checked:' Tailwind classes let CSS alone handle
                         the "selected" highlight, no JS needed. --}}
                    @foreach(['physical' => 'Physical', 'mental' => 'Mental', 'financial' => 'Financial', 'social' => 'Social'] as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="category" value="{{ $value }}" class="peer sr-only" {{ old('category') === $value ? 'checked' : '' }}>
                            <span class="block px-4 py-2 rounded-lg border border-gray-200 text-sm peer-checked:bg-wl-lavender peer-checked:border-wl-indigo peer-checked:text-wl-indigo">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('category') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Capacity</label>
                <input type="number" name="capacity" value="{{ old('capacity') }}"
                       class="w-full mt-1 border-gray-200 rounded-lg bg-gray-50 focus:ring-wl-indigo focus:border-wl-indigo">
                @error('capacity') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3"
                          class="w-full mt-1 border-gray-200 rounded-lg bg-gray-50 focus:ring-wl-indigo focus:border-wl-indigo">{{ old('description') }}</textarea>
            </div>

            <button class="w-full bg-wl-gold text-wl-navy font-semibold py-2.5 rounded-lg">
                Publish event
            </button>
        </form>
    </div>

    {{-- POINTER: a second, separate block below the form — lists every
         event so far with a delete button. Each delete is its own tiny
         <form> because HTML links can't send DELETE requests; Laravel's
         convention is a form with @method('DELETE') spoofing it, since
         browsers only natively support GET and POST on forms. --}}
    <h2 class="text-lg font-bold text-gray-900 mt-10 mb-4">Existing Events</h2>
    <div class="bg-white rounded-xl shadow-sm divide-y divide-gray-100 max-w-xl">
        @forelse($myEvents as $event)
            <div class="flex items-center justify-between px-5 py-3">
                <div>
                    <p class="font-medium text-gray-800 text-sm">{{ $event->name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ ucfirst($event->category) }} · {{ $event->starts_at->format('D, d M Y') }} · {{ $event->registrations_count }} registered
                    </p>
                </div>
                <form method="POST" action="{{ route('events.destroy', $event) }}"
                      onsubmit="return confirm('Delete {{ $event->name }}? This also removes its registrations, waivers, and feedback.');">
                    @csrf
                    @method('DELETE')
                    <button class="text-xs text-red-600 hover:underline">Delete</button>
                </form>
            </div>
        @empty
            <p class="px-5 py-4 text-sm text-gray-400">No events created yet.</p>
        @endforelse
    </div>
@endsection
