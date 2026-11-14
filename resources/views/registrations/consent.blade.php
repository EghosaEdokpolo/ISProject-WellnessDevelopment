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
            <span class="flex flex-col items-center"><span class="w-6 h-6 rounded-full bg-wl-gold text-white flex items-center justify-center mb-1">4</span> Consent</span>
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

        <h2 class="text-xl font-bold text-gray-900 mb-6">Data Consent Form</h2>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-600 h-32 overflow-y-auto mb-6">
            <strong>DATA PROTECTION NOTICE</strong><br><br>
            <strong>Data Controller:</strong> Strathmore University, Madaraka Estate, Ole Sangale Road, Nairobi.<br>
            <strong>Applicable Law:</strong> Kenya Data Protection Act, 2019.<br>
            <strong>Data Collected:</strong> Name, Staff ID, Department, WHO-5 wellbeing scores, attendance records, feedback responses.<br>
            <strong>Purpose:</strong> Programme impact measurement, HR reporting, institutional wellness management.
        </div>

        <form method="POST" action="{{ route('registrations.consent.store', $registration) }}">
            @csrf
            <label class="flex items-start gap-3 mb-8 cursor-pointer">
                <input type="checkbox" name="agree" class="w-4 h-4 mt-1 text-wl-indigo rounded border-gray-300 focus:ring-wl-indigo" required>
                <span class="text-sm text-gray-700">I consent to my data being processed as described above under the Kenya Data Protection Act 2019.</span>
            </label>
            <p class="text-xs text-gray-400 mb-6">Timestamp: {{ now()->format('d/m/Y H:i:s') }}</p>

            <div class="flex justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('registrations.waiver', $registration) }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-600 font-semibold text-sm hover:bg-gray-50">Back</a>
                <button type="submit" class="px-6 py-2.5 bg-wl-gold text-wl-navy font-bold text-sm rounded-lg hover:bg-yellow-600 transition">Submit & Get QR Code &rarr;</button>
            </div>
        </form>
    </div>
@endsection