@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('events.management') }}" class="text-xs font-extrabold text-[#02338d] hover:underline inline-flex items-center gap-1">
            ← Back to Event Management
        </a>
        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 bg-slate-100 px-3 py-1 rounded-full">
            HR Venue QR Code
        </span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden text-center">
        <div class="bg-[#02338d] text-white px-6 py-6">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-200 block">
                Strathmore University WellLoop
            </span>
            <h1 class="text-xl font-extrabold tracking-tight mt-1">{{ $event->name }}</h1>
            <p class="text-xs text-blue-100 italic mt-1">
                {{ $eventStart->format('l, d M Y \a\t H:i') }} EAT • {{ $event->location }}
            </p>
        </div>

        <div class="p-8">
            <p class="text-xs text-slate-600 mb-6 font-semibold">
                Project this QR code on the venue screen or print it for the entrance door. Staff scan it with their phone cameras to auto-register and check in.
            </p>

            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-200 inline-block mx-auto shadow-inner mb-6">
                <div id="qrcode-canvas" class="bg-white p-4 rounded-2xl shadow-sm inline-block"></div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 text-left mb-6">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">Signed Check-In Target URL</span>
                <code class="text-[11px] text-[#02338d] font-mono break-all select-all block bg-white p-2.5 rounded-xl border border-slate-200">
                    {{ $scanUrl }}
                </code>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('events.qr.poster', $event) }}"
                   target="_blank"
                   class="flex-1 bg-[#c99a2e] hover:bg-yellow-500 text-[#01225e] text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                    Print A4 Poster View →
                </a>
                <button type="button"
                        onclick="window.print()"
                        class="flex-1 bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold py-3.5 rounded-xl shadow-sm transition">
                    Print QR Code
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const qrContainer = document.getElementById('qrcode-canvas');
        if (!qrContainer) return;

        qrContainer.innerHTML = '';

        const canvas = document.createElement('canvas');
        qrContainer.appendChild(canvas);

        new QRious({
            element: canvas,
            value: @json($scanUrl),
            size: 240,
            level: 'H',
            background: '#ffffff',
            foreground: '#02338d'
        });
    });
</script>
@endpush