<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Strathmore University WellLoop') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'su-blue': '#02338d',
                        'su-blue-dark': '#012466',
                        'su-gold': '#c99a2e',
                        'su-gold-tint': '#f5eccd',
                        'su-red': '#e03c31',
                        'su-black': '#1d1d1b',
                        'brand': '#02338d',
                        'brand-light': '#e8f0fe',
                        'wl-gold': '#c99a2e',
                        'wl-indigo': '#02338d'
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                        wordmark: ['Sansation', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8f9fb;
            color: #1d1d1b;
        }
        .font-wordmark {
            font-family: 'Sansation', sans-serif;
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @stack('styles')
</head>
<body class="bg-[#f8f9fb] text-[#1d1d1b] antialiased min-h-screen flex flex-col">

    @php
        $user = auth()->user();
        $isHr = $user !== null && method_exists($user, 'isHrCoordinator') && $user->isHrCoordinator();
    @endphp

    @if($isHr)
        <x-hr-nav />
    @else
        <div class="bg-[#02338d] text-white px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 text-xs font-bold tracking-wide shadow-sm">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" class="h-5 w-5 object-contain" alt="Strathmore" onerror="this.style.display='none'">
                <span class="font-wordmark tracking-wider">WPMIS — STRATHMORE UNIVERSITY WELLNESS MODULE</span>
            </div>
            <span class="bg-[#c99a2e] text-[#012466] px-3 py-0.5 rounded-full font-extrabold text-[11px]">Staff Portal</span>
        </div>

        <header class="bg-white border-b border-slate-200 px-6 py-3 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">

                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-3 shrink-0">
                    <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" class="h-9 w-9 object-contain" alt="Strathmore" onerror="this.style.display='none'">
                    <div class="leading-tight">
                        <span class="block text-sm font-extrabold text-[#02338d] font-wordmark">Strathmore University</span>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">WellLoop • People & Culture</span>
                    </div>
                </a>

                @auth
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('events.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('events.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Events</a>
                        <a href="{{ route('checkin.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('checkin.*', 'registrations.ticket') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Check-In</a>
                        <a href="{{ route('feedback.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('feedback.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Feedback</a>
                        <a href="{{ route('rewards.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('rewards.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Rewards</a>
                        <a href="{{ route('content.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('content.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Content Hub</a>
                    </nav>

                    <!-- User Profile & Clean Institutional Sign Out Button -->
                    <div class="hidden md:flex items-center gap-3 border-l border-slate-200 pl-4">
                        <span class="text-[11px] font-extrabold text-[#8a6514] bg-[#f5eccd] px-2.5 py-1 rounded-full">
                            {{ number_format((int) (auth()->user()->points_balance ?? 0)) }} pts
                        </span>
                        <a href="{{ route('profile.edit') }}" class="text-xs font-extrabold text-slate-700 hover:text-[#02338d] transition">
                            {{ auth()->user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    title="Sign out of WellLoop"
                                    class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-[#e03c31] text-slate-700 hover:text-white border border-slate-200 hover:border-[#e03c31] px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all duration-150">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>

                    <button type="button" onclick="document.getElementById('mobile-staff-drawer').classList.toggle('hidden')" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                @endauth
            </div>

            @auth
                <div id="mobile-staff-drawer" class="hidden md:hidden pb-3 pt-3 mt-2 border-t border-slate-100 space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('events.index') }}" class="px-3 py-2 rounded-lg text-xs font-extrabold {{ request()->routeIs('events.*') ? 'bg-[#02338d] text-white' : 'text-slate-700 bg-slate-50' }}">Events</a>
                        <a href="{{ route('checkin.index') }}" class="px-3 py-2 rounded-lg text-xs font-extrabold {{ request()->routeIs('checkin.*', 'registrations.ticket') ? 'bg-[#02338d] text-white' : 'text-slate-700 bg-slate-50' }}">Check-In</a>
                        <a href="{{ route('feedback.index') }}" class="px-3 py-2 rounded-lg text-xs font-extrabold {{ request()->routeIs('feedback.*') ? 'bg-[#02338d] text-white' : 'text-slate-700 bg-slate-50' }}">Feedback</a>
                        <a href="{{ route('rewards.index') }}" class="px-3 py-2 rounded-lg text-xs font-extrabold {{ request()->routeIs('rewards.*') ? 'bg-[#02338d] text-white' : 'text-slate-700 bg-slate-50' }}">Rewards</a>
                        <a href="{{ route('content.index') }}" class="px-3 py-2 rounded-lg text-xs font-extrabold {{ request()->routeIs('content.*') ? 'bg-[#02338d] text-white' : 'text-slate-700 bg-slate-50' }}">Content Hub</a>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between px-2">
                        <a href="{{ route('profile.edit') }}" class="text-xs font-extrabold text-[#02338d]">{{ auth()->user()->name }} ({{ (int) auth()->user()->points_balance }} pts)</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 bg-rose-50 text-[#e03c31] border border-rose-200 px-3 py-1.5 rounded-lg text-xs font-extrabold">
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </header>

        @includeIf('components.staff-reminders-banner')
    @endif

    <main class="flex-1 py-6 px-4 sm:px-6">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white mt-12">
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

    @stack('scripts')
</body>
</html>