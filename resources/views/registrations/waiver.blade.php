@extends('layouts.app')

@section('content')
    <div class="mb-8 text-center">
        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-wl-indigo hover:underline flex items-center justify-center gap-1 mb-6">&larr; Back to events</a>
        <div class="flex justify-center items-center gap-4 text-xs font-semibold text-gray-400 mb-8">
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-indigo text-white flex items-center justify-center mb-1">✓</span> Details</span>
            <span class="w-12 h-px bg-wl-indigo"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-indigo text-white flex items-center justify-center mb-1">✓</span> Baseline</span>
            <span class="w-12 h-px bg-wl-indigo"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-gold text-white flex items-center justify-center mb-1">3</span> Waiver</span>
            <span class="w-12 h-px bg-gray-300"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mb-1">4</span> Consent</span>
            <span class="w-12 h-px bg-gray-300"></span>
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mb-1">5</span> Ticket</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-2xl mx-auto">
        <div class="bg-wl-lavender/40 rounded-lg p-4 mb-6 flex items-center gap-3">
            <span class="bg-wl-lavender text-wl-indigo text-[10px] font-bold px-2 py-1 rounded">{{ ucfirst($registration->event->category) }}</span>
            <div>
                <h3 class="font-bold text-gray-900">{{ $registration->event->name }}</h3>
                <p class="text-xs text-gray-600">{{ $registration->event->starts_at->format('D, d M Y') }} · {{ $registration->event->location }}</p>
            </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900 mb-6">Indemnity Waiver</h2>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-600 h-32 overflow-y-auto mb-6">
            <strong>STRATHMORE UNIVERSITY — WELLNESS EVENT INDEMNITY WAIVER</strong><br><br>
            I, the undersigned, acknowledge that participation in the above-named wellness event is voluntary. I understand that physical activities involve inherent risks. I hereby indemnify and hold harmless Strathmore University, its officers, employees, and agents from any claims arising from my participation.
        </div>

        <form method="POST" action="{{ route('registrations.waiver.store', $registration) }}">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Type your full name as signature</label>
                <input type="text" name="signature_name" placeholder="e.g. {{ auth()->user()->name }}" class="w-full border-gray-300 rounded-lg bg-gray-50 p-3 text-sm focus:ring-wl-indigo focus:border-wl-indigo" required>
                <p class="text-xs text-gray-400 mt-2">Signed at: {{ now()->format('d/m/Y, H:i A T') }}</p>
            </div>

            <label class="flex items-center gap-3 mb-8 cursor-pointer">
                <input type="checkbox" name="agree" class="w-4 h-4 text-wl-indigo rounded border-gray-300 focus:ring-wl-indigo" required>
                <span class="text-sm text-gray-700">I have read, understood, and agree to the indemnity waiver above.</span>
            </label>

            <div class="flex justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('registrations.who5', $registration) }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-600 font-semibold text-sm hover:bg-gray-50">Back</a>
                <button type="submit" class="px-6 py-2.5 bg-wl-gold text-wl-navy font-bold text-sm rounded-lg hover:bg-yellow-600 transition">Continue &rarr;</button>
            </div>
        </form>
    </div>
@endsection