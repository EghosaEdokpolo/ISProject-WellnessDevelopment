@extends('layouts.app')

@section('content')
@php
    $tz = (string) config('wellloop.timezone', 'Africa/Nairobi');
    $eventStart = $registration->event->starts_at->copy()->shiftTimezone($tz);
@endphp

<div class="max-w-3xl mx-auto px-4 py-8">

    <!-- Breadcrumb -->
    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-xs font-extrabold text-slate-500 hover:text-[#02338d] transition mb-6 group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Events
    </a>

    @if(session('warning'))
        <div class="mb-6 bg-amber-50 text-amber-900 border border-amber-200 px-5 py-3.5 rounded-2xl text-xs font-bold">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Event Info Header -->
    <div class="mb-6">
        <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#02338d] bg-[#e8f0fe] px-3 py-1 rounded-full">
            {{ strtoupper($registration->event->category) }}
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">{{ $registration->event->name }}</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1 italic">{{ $eventStart->format('l, d M Y \a\t H:i') }} EAT · {{ $registration->event->location }}</p>
    </div>

    <!-- Waiver Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-[#02338d] px-8 py-6 text-white relative overflow-hidden">
            <h2 class="text-xl font-extrabold relative z-10 flex items-center gap-3">
                <svg class="w-6 h-6 text-[#c99a2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Mandatory Participation & Indemnity Waiver
            </h2>
            <p class="text-blue-100 text-xs mt-1 relative z-10">Please review carefully and sign digitally below to secure your session pass (+5 pts).</p>
        </div>

        <div class="p-6 sm:p-8">
            <form method="POST" action="{{ route('registrations.waiver.store', $registration) }}">
                @csrf

                <!-- Waiver Text -->
                <div class="mb-6">
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-xs sm:text-sm text-slate-600 leading-relaxed italic overflow-y-auto max-h-48 shadow-inner">
                        <p>I, the undersigned staff member of Strathmore University, acknowledge that participation in the above-named wellness session is voluntary. I understand that wellness and physical activities involve inherent risks. I hereby indemnify and hold harmless Strathmore University, its People & Culture department, officers, employees, and facilitators from any claims arising from my participation.</p>
                    </div>
                </div>

                <!-- Signature -->
                <div class="mb-6">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-600 mb-2">Type your full name as your digital signature</label>
                    <input type="text"
                           name="signature_name"
                           value="{{ old('signature_name', auth()->user()->name) }}"
                           required
                           placeholder="e.g., {{ auth()->user()->name }}"
                           class="w-full border border-slate-200 rounded-xl text-sm p-3.5 bg-slate-50 focus:bg-white focus:ring-[#02338d] focus:border-[#02338d] font-bold">
                    @error('signature_name') <span class="text-[#e03c31] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Agreement Checkbox -->
                <div class="mb-8 bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-start gap-3">
                    <input type="checkbox" name="agree" id="agree" required class="mt-0.5 w-4 h-4 text-[#02338d] border-slate-300 rounded focus:ring-[#02338d] cursor-pointer">
                    <label for="agree" class="text-xs sm:text-sm font-semibold text-slate-700 cursor-pointer select-none">
                        I have read, understood, and agree to the Strathmore University participation and indemnity waiver above.
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-[#c99a2e] hover:bg-[#b58924] text-[#012466] font-extrabold text-sm py-4 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                    <span>Sign Waiver & Confirm Registration (+5 Pts)</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection