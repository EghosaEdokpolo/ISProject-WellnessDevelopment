@extends('layouts.app')

@section('content')
    <div class="mb-8 text-center">
        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-wl-indigo hover:underline flex items-center justify-center gap-1 mb-6">&larr; Back to events</a>
        <div class="flex justify-center items-center gap-4 text-xs font-semibold text-gray-400 mb-8">
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-indigo text-white flex items-center justify-center mb-1">✓</span> Details</span>
            <span class="w-12 h-px bg-wl-indigo"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-indigo text-white flex items-center justify-center mb-1">✓</span> Baseline</span>
            <span class="w-12 h-px bg-wl-indigo"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-indigo text-white flex items-center justify-center mb-1">✓</span> Waiver</span>
            <span class="w-12 h-px bg-wl-indigo"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-indigo text-white flex items-center justify-center mb-1">✓</span> Consent</span>
            <span class="w-12 h-px bg-wl-indigo"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-gold text-white flex items-center justify-center mb-1">5</span> Ticket</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-2xl mx-auto text-center">
        <div class="bg-wl-lavender/40 rounded-lg p-4 mb-6 inline-flex items-center gap-3 text-left w-full">
            <span class="bg-wl-lavender text-wl-indigo text-[10px] font-bold px-2 py-1 rounded">{{ ucfirst($registration->event->category) }}</span>
            <div>
                <h3 class="font-bold text-gray-900">{{ $registration->event->name }}</h3>
                <p class="text-xs text-gray-600">{{ $registration->event->starts_at->format('D, d M Y') }} · {{ $registration->event->location }}</p>
            </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900 mb-2">You are registered!</h2>
        <p class="text-sm text-gray-500 mb-8">Present this QR code at the event entrance. It refreshes every 15 minutes to prevent gaming.</p>

        <div class="bg-white border-2 border-gray-100 rounded-xl p-4 inline-block mb-4 shadow-sm">
            {!! $qrSvg !!}
        </div>

        <div class="mb-8">
            <p class="font-bold text-gray-900 text-lg">{{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-500">{{ $registration->event->name }}</p>
        </div>

        <div class="bg-red-50 text-red-600 text-xs rounded-lg px-4 py-3 mb-6 inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Code refreshes in 14:23 — screenshot won't work after expiry
        </div>

        <!-- UPDATED TO SHOW STAFF ID -->
        <p class="text-xs text-gray-500 mb-6">
            No smartphone? Enter <strong>{{ auth()->user()->staff_id }}</strong> manually at the check-in desk. (WCAG 2.1 AA non-QR fallback)
        </p>

        <div class="flex justify-start pt-6 border-t border-gray-100">
            <a href="{{ route('events.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-600 font-semibold text-sm hover:bg-gray-50">Back</a>
        </div>
    </div>
@endsection