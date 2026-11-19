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

    <!-- Header -->
    <header class="bg-[#02338d] border-b border-[#01225e] px-6 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" class="h-10 w-10 object-contain" alt="Strathmore" onerror="this.style.display='none'">
                <div>
                    <span class="block text-base font-extrabold text-white font-wordmark">Strathmore University</span>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-blue-200">WellLoop • Powered by People & Culture</span>
                </div>
            </div>

            @auth
                <a href="{{ route('dashboard') }}" class="bg-white hover:bg-blue-50 text-[#02338d] text-xs font-extrabold px-5 py-2.5 rounded-xl shadow-sm transition">
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
                Register for campus sessions, check in via venue QR code, track your wellbeing with the WHO-5 index, and earn recognition points - All built exclusively for Strathmore University staff.
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

    <!-- Contact Us Centered Footer -->
    <footer class="bg-[#02338d] text-white mt-auto">
        <div class="max-w-4xl mx-auto px-6 py-10 text-center flex flex-col items-center justify-center space-y-4">
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Contact Us</h2>

            <p class="text-xs sm:text-sm text-white/95 leading-relaxed max-w-xl">
                Madaraka Estate Ole Sangale Road, PO Box 59857, 00200 City Square Nairobi, Kenya
            </p>

            <div class="flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm text-white/95">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-white text-[#02338d] shrink-0">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1C10.61 21 3 13.39 3 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.46.57 3.58a1 1 0 01-.25 1.01l-2.2 2.2z"/>
                    </svg>
                </span>
                <span>Phone : (+254) (0)703-034000 (+254) (0)703-034200 (+254) (0)703-034300</span>
            </div>

            <div class="flex items-center justify-center gap-2 text-xs sm:text-sm text-white/95">
                <svg class="w-5 h-5 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                <span>
                    Email :
                    <a href="mailto:systems@strathmore.edu" class="underline hover:text-[#f5eccd] transition">
                        systems@strathmore.edu
                    </a>
                </span>
            </div>
        </div>
    </footer>
</body>
</html>