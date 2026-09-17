@extends('layouts.app')

@section('content')
    <div class="bg-wl-lavender rounded-lg px-5 py-4 mb-6 max-w-xl">
        <span class="text-xs font-semibold text-wl-indigo uppercase">{{ $registration->event->category }}</span>
        <h2 class="font-bold text-gray-900">{{ $registration->event->name }}</h2>
        <p class="text-xs text-gray-500">{{ $registration->event->starts_at->format('D, d M Y') }} · {{ $registration->event->location }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 max-w-xl">
        <h3 class="font-semibold text-gray-900 mb-3">Indemnity Waiver</h3>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-600 h-32 overflow-y-auto mb-4">
            STRATHMORE UNIVERSITY — WELLNESS EVENT INDEMNITY WAIVER<br><br>
            I, the undersigned, acknowledge that participation in the above-named wellness
            event is voluntary. I understand that physical activities involve inherent risks.
            I hereby indemnify and hold harmless Strathmore University, its officers,
            employees, and agents from any claims arising from my participation.
        </div>

        <form method="POST" action="{{ route('registrations.waiver.store', $registration) }}" class="space-y-4">
            @csrf

            <div>
                <label class="text-sm font-medium text-gray-700">Type your full name as signature</label>
                <input name="signature_name" class="w-full mt-1 border-gray-200 rounded-lg bg-gray-50 focus:ring-wl-indigo focus:border-wl-indigo">
                @error('signature_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="agree" class="rounded text-wl-indigo focus:ring-wl-indigo">
                I have read, understood, and agree to the indemnity waiver above.
            </label>
            @error('agree') <p class="text-xs text-red-600 -mt-2">{{ $message }}</p> @enderror

            <button class="w-full bg-wl-gold text-wl-navy font-semibold py-2.5 rounded-lg">
                Continue →
            </button>
        </form>
    </div>
@endsection
