<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WPMIS — Staff Portal</title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#161a33">
    
    <!-- Strathmore Brand Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'wl-navy':     '#161a33',
                        'wl-indigo':   '#5b5fc7',
                        'wl-gold':     '#c99a2e',
                        'wl-cream':    '#f6f4ee',
                        'wl-lavender': '#e4e2fb',
                        'wl-mint':     '#dcf3e4',
                        'wl-lilac':    '#f1e4fa',
                    },
                    fontFamily: {
                        sans: ['Lato', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-wl-cream min-h-screen font-sans">

    <div class="bg-wl-navy text-wl-gold text-[10px] tracking-wide font-semibold px-6 py-2 flex justify-between items-center">
        <span>WPMIS — WELLNESS PROGRAMME MANAGEMENT &amp; IMPACT SYSTEM</span>
        @auth
            <div class="flex items-center gap-3">
                <span class="bg-white/10 text-white rounded-full px-3 py-1 uppercase">Staff Member</span>
                <span class="text-white font-normal">{{ auth()->user()->name }}</span>
            </div>
        @endauth
    </div>

    <nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 bg-wl-indigo rounded-lg flex items-center justify-center text-white font-bold">S</div>
            <div>
                <div class="font-bold text-wl-indigo text-sm leading-tight">Strathmore University</div>
                <div class="text-[10px] text-gray-500 leading-tight uppercase tracking-wider">WellLoop · Powered by People &amp; Culture</div>
            </div>
        </div>

        <div class="flex items-center gap-1 bg-white p-1 rounded-xl">
            <a href="{{ route('events.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('events.index') ? 'bg-wl-indigo text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('events.index') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Events
            </a>

            <a href="{{ route('checkin.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('checkin.*') ? 'bg-wl-indigo text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('checkin.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                Check-in
            </a>

            <a href="{{ route('feedback.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('feedback.*') ? 'bg-wl-indigo text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('feedback.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Feedback
            </a>

            <a href="{{ route('rewards.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('rewards.index') ? 'bg-wl-indigo text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('rewards.index') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
                Rewards
            </a>

            <a href="{{ route('content.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('content.*') ? 'bg-wl-indigo text-white' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('content.*') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Content Hub
            </a>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-xs text-gray-500 hover:text-red-600 font-semibold">Log out</button>
        </form>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-8">
        @if(session('status'))
            <div class="mb-6 bg-wl-mint text-emerald-800 px-4 py-3 rounded-lg text-sm font-medium shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>