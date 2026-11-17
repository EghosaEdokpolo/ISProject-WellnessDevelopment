@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-12">
    
    <!-- Breadcrumb -->
    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-brand transition mb-8 group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Events
    </a>

    <!-- Event Info Header -->
    <div class="mb-8">
        <span class="text-[10px] font-bold uppercase tracking-wider text-brand bg-brand-light px-3 py-1 rounded-full">
            {{ $registration->event->category }}
        </span>
        <h1 class="text-3xl font-extrabold text-slate-900 mt-3">{{ $registration->event->name }}</h1>
        <p class="text-slate-500 mt-1 font-medium">{{ $registration->event->starts_at->format('D, d M Y \a\t H:i') }} · {{ $registration->event->location }}</p>
    </div>

    <!-- Waiver Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-brand px-8 py-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <h2 class="text-2xl font-bold relative z-10 flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Indemnity Waiver
            </h2>
            <p class="text-brand-light text-sm mt-1 relative z-10">Please read carefully and sign below to secure your spot.</p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('registrations.waiver.store', $registration) }}">
                @csrf
                
                <!-- Waiver Text -->
                <div class="mb-6">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-sm text-slate-600 leading-relaxed italic overflow-y-auto max-h-48 shadow-inner">
                        <p>I, the undersigned, acknowledge that participation in the above-named wellness event is voluntary. I understand that physical activities involve inherent risks. I hereby indemnify and hold harmless Strathmore University, its officers, employees, and agents from any claims arising from my participation.</p>
                    </div>
                </div>

                <!-- Signature -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Type your full name as signature</label>
                    <input type="text" name="signature_name" required placeholder="e.g., John Doe"
                           class="w-full border-slate-200 rounded-xl text-sm p-3.5 bg-slate-50 focus:ring-brand focus:border-brand font-medium">
                    @error('signature_name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Agreement Checkbox -->
                <div class="mb-8 bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-start gap-3">
                    <input type="checkbox" name="agree" id="agree" required class="mt-0.5 w-4 h-4 text-brand border-slate-300 rounded focus:ring-brand cursor-pointer">
                    <label for="agree" class="text-sm font-medium text-slate-700 cursor-pointer select-none">
                        I have read, understood, and agree to the indemnity waiver above.
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-wl-gold text-brand font-extrabold text-lg py-4 rounded-xl shadow-sm hover:bg-yellow-600 transition flex items-center justify-center gap-2">
                    Continue
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection