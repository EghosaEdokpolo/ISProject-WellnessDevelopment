@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900">Attendee Table</h1>
    <p class="text-sm text-gray-500 mt-1 mb-6">Full participation record across all events.</p>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-wl-indigo text-white text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Event</th>
                    <th class="px-4 py-3 font-medium">Department</th>
                    <th class="px-4 py-3 font-medium text-center">Waiver</th>
                    <th class="px-4 py-3 font-medium text-center">Checked in</th>
                    <th class="px-4 py-3 font-medium text-center">Feedback</th>
                    <th class="px-4 py-3 font-medium text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $reg->user->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $reg->event->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $reg->user->department ?? '—' }}</td>

                        {{-- POINTER: $reg->waiver is null if no Waiver row
                             exists for this registration (the hasOne
                             relationship from the Registration model).
                             Checking truthiness like this is a common,
                             concise pattern for "has this step happened yet?" --}}
                        <td class="px-4 py-3 text-center">
                            @if($reg->waiver) <span class="text-emerald-600">✓</span> @else <span class="text-gray-300">—</span> @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($reg->checkIn) <span class="text-emerald-600">✓</span> @else <span class="text-gray-300">—</span> @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($reg->feedback) <span class="text-emerald-600">✓</span> @else <span class="text-gray-300">—</span> @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2 py-1 rounded-full
                                {{ $reg->status === 'completed' ? 'bg-wl-mint text-emerald-700' : ($reg->status === 'checked_in' ? 'bg-wl-lavender text-wl-indigo' : 'bg-gray-100 text-gray-500') }}">
                                {{ ucfirst(str_replace('_', ' ', $reg->status)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            No registrations yet — once staff register for events, they'll show up here.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
