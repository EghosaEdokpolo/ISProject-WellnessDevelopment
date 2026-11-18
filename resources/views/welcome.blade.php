<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strathmore University WellLoop — Staff Wellness Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Montserrat', sans-serif; color: #1d1d1b; }
        .font-wordmark { font-family: 'Sansation', sans-serif; }
    </style>
</head>
<body class="bg-[#f8f9fb] min-h-screen flex flex-col">

    <!-- Top Institutional Bar -->
    <div class="bg-[#02338d] text-white px-6 py-2.5 flex items-center justify-between text-xs font-bold tracking-wider">
        <div class="flex items-center gap-2.5">
            <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" class="h-5 w-5 object-contain" alt="Strathmore" onerror="this.style.display='none'">
            <span class="font-wordmark">WPMIS — STRATHMORE UNIVERSITY PEOPLE & CULTURE</span>
        </div>
        <span class="text-[#c99a2e] font-extrabold text-[11px] hidden sm:inline">Ut omnes unum sint</span>
    </div>

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" class="h-10 w-10 object-contain" alt="Strathmore" onerror="this.style.display='none'">
                <div>
                    <span class="block text-base font-extrabold text-[#02338d] font-wordmark">Strathmore University</span>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">WellLoop • Powered by People & Culture</span>
                </div>
            </div>

            @auth
                <a href="{{ route('dashboard') }}" class="bg-[#02338d] hover:bg-[#01225e] text-white text-xs font-extrabold px-5 py-2.5 rounded-xl shadow-sm transition">
                    Open Dashboard →
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-[#c99a2e] hover:bg-[#b58924] text-[#01225e] text-xs font-extrabold px-5 py-2.5 rounded-xl shadow-sm transition">
                    Log In
                </a>
            @endauth
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1">
        <section class="max-w-5xl mx-auto px-6 py-16 text-center">
            <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-[#02338d] bg-[#e8f0fe] px-3.5 py-1.5 rounded-full">
                Strathmore Staff Wellbeing Ecosystem
            </span>

            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight mt-4 leading-tight">
                Staff wellness, <span class="text-[#02338d]">measured and managed</span> in one place
            </h1>

            <p class="text-sm sm:text-base text-slate-600 max-w-2xl mx-auto mt-4 leading-relaxed">
                Register for campus sessions, check in via venue QR code, track your wellbeing with the WHO-5 index, and earn recognition points — built exclusively for Strathmore University staff.
            </p>

            <div class="mt-8">
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
                   class="inline-flex items-center gap-2 bg-[#02338d] hover:bg-[#01225e] text-white text-xs sm:text-sm font-extrabold px-8 py-4 rounded-2xl shadow-md transition">
                    <span>Log in to WellLoop</span>
                    <span>→</span>
                </a>
            </div>

            <!-- Pillar Highlights -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-14 text-left">
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                    <span class="text-2xl">🏃</span>
                    <h3 class="text-sm font-extrabold text-[#02338d] mt-3">Physical Wellness</h3>
                    <p class="text-xs text-slate-500 mt-1">Campus step challenges, fitness coaching, and mobility sessions.</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                    <span class="text-2xl">🧠</span>
                    <h3 class="text-sm font-extrabold text-emerald-700 mt-3">Mental & Emotional</h3>
                    <p class="text-xs text-slate-500 mt-1">Mindfulness labs, stress resilience clinics, and WHO-5 tracking.</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                    <span class="text-2xl">💰</span>
                    <h3 class="text-sm font-extrabold text-[#8a6514] mt-3">Financial Wellbeing</h3>
                    <p class="text-xs text-slate-500 mt-1">SACCO workshops, retirement planning, and investment forums.</p>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                    <span class="text-2xl">🤝</span>
                    <h3 class="text-sm font-extrabold text-[#02338d] mt-3">Social & Community</h3>
                    <p class="text-xs text-slate-500 mt-1">Inter-faculty mixers, sustainability walks, and peer support.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-wordmark font-bold text-[#1d1d1b] text-sm">Strathmore</span>
                <span class="font-wordmark tracking-widest uppercase text-[11px] text-slate-600">University</span>
                <span class="text-slate-300">|</span>
                <span class="italic text-[11px]">Ut omnes unum sint</span>
            </div>
            <div class="text-[11px] font-medium">
                Wellness Performance Management Information System • Ole Sangale Road, Madaraka Estate, Nairobi
            </div>
        </div>
    </footer>
</body>
</html>