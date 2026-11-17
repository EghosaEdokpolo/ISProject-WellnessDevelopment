<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Poster — {{ $event->name }} | Strathmore University</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body, h1, h2, h3, p, div, span, a, button {
            font-family: 'Sansation', sans-serif !important;
        }
        @media print {
            body { background: #ffffff !important; }
            .no-print { display: none !important; }
            #printable-poster {
                box-shadow: none !important;
                border: 2px solid #02338d !important;
                margin: 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4">

    <div class="max-w-2xl mx-auto">
        <!-- Top Action Bar (Hidden when printing) -->
        <div class="no-print flex flex-wrap items-center justify-between gap-3 mb-6 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <a href="{{ route('events-management.index') }}" class="text-xs font-extrabold text-[#02338d] hover:underline">
                ← Back to Manage Events
            </a>
            <button type="button"
                    onclick="window.print()"
                    class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-6 py-3 rounded-xl shadow-sm transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Venue QR Poster
            </button>
        </div>

        <!-- Printable A4 Poster -->
        <div id="printable-poster" class="bg-white rounded-3xl border-2 border-slate-200 p-8 sm:p-12 shadow-sm text-center">
            <div class="flex justify-center mb-5">
                <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" alt="Strathmore University" class="h-14 w-auto">
            </div>

            <div class="inline-block text-[11px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-4 py-1.5 rounded-full mb-4">
                Strathmore University • People & Culture Wellness
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                {{ $event->name }}
            </h1>

            <p class="text-sm font-bold text-slate-600 mt-2">
                {{ $event->starts_at->format('l, d M Y \a\t H:i') }} • {{ $event->location }}
            </p>

            <!-- High-Contrast QR Code Box -->
            <div class="my-8 flex flex-col items-center justify-center">
                <div class="p-6 bg-white border-4 border-[#02338d] rounded-3xl shadow-sm">
                    <div id="event-qr-code"></div>
                </div>
                <span class="mt-4 text-xs font-extrabold uppercase tracking-wider bg-[#c99a2e]/20 text-amber-900 px-4 py-1.5 rounded-full">
                    Scan to Register & Check In (+{{ $event->points_checkin ?? 20 }} Points)
                </span>
            </div>

            <!-- Instructions -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 max-w-md mx-auto text-left space-y-2 text-xs text-slate-700">
                <p class="font-extrabold text-[#02338d] text-sm">Instant Venue Check-In:</p>
                <p>1. Open your phone camera and scan the QR code above.</p>
                <p>2. Log in with your Strathmore Staff ID if prompted.</p>
                <p>3. You will be registered and checked in automatically.</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new QRCode(document.getElementById('event-qr-code'), {
                text: @json($scanUrl),
                width: 250,
                height: 250,
                colorDark: '#02338d',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>