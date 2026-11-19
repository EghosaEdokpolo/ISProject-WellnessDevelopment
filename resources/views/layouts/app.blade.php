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

        /* Prevent iOS Safari automatic viewport zoom when focusing inputs on mobile */
        @media (max-width: 639px) {
            input, select, textarea {
                font-size: 16px !important;
            }
        }
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
        <!-- Unified Staff Header (Strathmore Blue — Safe Down to 320px Phone & Half-Screen Laptop) -->
        <header id="staff-main-header" class="bg-[#02338d] border-b border-[#01225e] px-3 sm:px-6 py-3 sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-2 sm:gap-4">

                <!-- Left: Strathmore University Branding -->
                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                    <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" class="h-8 w-8 sm:h-9 sm:w-9 object-contain shrink-0" alt="Strathmore" onerror="this.style.display='none'">
                    <div class="leading-tight min-w-0">
                        <span class="block text-xs sm:text-sm font-extrabold text-white font-wordmark truncate">Strathmore University</span>
                        <span class="block text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-blue-200 truncate">WellLoop • People & Culture</span>
                    </div>
                </a>

                @auth
                    <!-- Center: Staff Navigation (1024px+) -->
                    <nav class="hidden lg:flex items-center gap-1 whitespace-nowrap">
                        <a href="{{ route('events.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('events.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Events</a>
                        <a href="{{ route('checkin.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('checkin.*', 'registrations.ticket') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Check-In</a>
                        <a href="{{ route('feedback.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('feedback.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Feedback</a>
                        <a href="{{ route('rewards.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('rewards.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Rewards</a>
                        <a href="{{ route('content.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('content.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Content Hub</a>
                    </nav>

                    <!-- Right: Full Desktop Profile & Sign Out (1280px+) -->
                    <div class="hidden xl:flex items-center gap-3 border-l border-white/20 pl-4 shrink-0">
                        <span class="text-[11px] font-extrabold text-[#01225e] bg-[#c99a2e] px-2.5 py-1 rounded-full">
                            Staff Portal
                        </span>
                        <span class="text-[11px] font-extrabold text-white bg-white/15 border border-white/20 px-2.5 py-1 rounded-full">
                            {{ number_format((int) (auth()->user()->points_balance ?? 0)) }} pts
                        </span>
                        <a href="{{ route('profile.edit') }}" class="text-xs font-extrabold text-white hover:text-[#f5eccd] transition">
                            {{ auth()->user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    title="Sign out of WellLoop"
                                    class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-[#e03c31] text-white border border-white/20 hover:border-[#e03c31] px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all duration-150">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>

                    <!-- Compact Right Section (1024px–1279px) -->
                    <div class="hidden lg:flex xl:hidden items-center gap-2.5 border-l border-white/20 pl-3 shrink-0">
                        <span class="text-[11px] font-extrabold text-[#01225e] bg-[#c99a2e] px-2 py-0.5 rounded-full">
                            Staff
                        </span>
                        <a href="{{ route('profile.edit') }}" class="text-xs font-extrabold text-white hover:text-[#f5eccd] transition max-w-[110px] truncate">
                            {{ auth()->user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    title="Sign out of WellLoop"
                                    class="inline-flex items-center gap-1 bg-white/10 hover:bg-[#e03c31] text-white border border-white/20 hover:border-[#e03c31] px-2.5 py-1.5 rounded-xl text-xs font-extrabold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>

                    <!-- Responsive Menu Toggle (Below 1024px: Half-Screen, Tablet, Mobile) -->
                    <button type="button"
                            id="staff-drawer-toggle"
                            aria-expanded="false"
                            aria-controls="mobile-staff-drawer"
                            onclick="toggleStaffNavDrawer()"
                            class="lg:hidden inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 px-2.5 sm:px-3 py-2 rounded-xl text-xs font-extrabold transition shrink-0">
                        <svg id="staff-icon-open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg id="staff-icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="hidden sm:inline">Menu</span>
                    </button>
                @endauth
            </div>

            <!-- Collapsible Staff Drawer (Below 1024px) -->
            @auth
                <div id="mobile-staff-drawer" class="hidden lg:hidden max-w-7xl mx-auto pb-2 pt-4 mt-3 border-t border-white/15 space-y-3">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <a href="{{ route('events.index') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('events.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Events</a>
                        <a href="{{ route('checkin.index') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('checkin.*', 'registrations.ticket') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Check-In</a>
                        <a href="{{ route('feedback.index') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('feedback.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Feedback</a>
                        <a href="{{ route('rewards.index') }}" class="px-3.5 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('rewards.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Rewards</a>
                        <a href="{{ route('content.index') }}" class="col-span-2 sm:col-span-1 px-3.5 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('content.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Content Hub</a>
                    </div>
                    <div class="pt-3 border-t border-white/15 flex flex-wrap items-center justify-between px-1 gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-[10px] font-extrabold text-[#01225e] bg-[#c99a2e] px-2.5 py-0.5 rounded-full shrink-0">Staff Portal</span>
                            <a href="{{ route('profile.edit') }}" class="text-xs font-extrabold text-white hover:text-[#f5eccd] truncate">
                                {{ auth()->user()->name }} ({{ number_format((int) auth()->user()->points_balance) }} pts)
                            </a>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 bg-[#e03c31] hover:bg-red-700 text-white px-3.5 py-2 rounded-xl text-xs font-extrabold transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </header>

        <script>
            function toggleStaffNavDrawer(forceClose = false) {
                const drawer   = document.getElementById('mobile-staff-drawer');
                const btn      = document.getElementById('staff-drawer-toggle');
                const icoOpen  = document.getElementById('staff-icon-open');
                const icoClose = document.getElementById('staff-icon-close');
                if (!drawer || !btn) return;

                const isHidden   = drawer.classList.contains('hidden');
                const shouldOpen = forceClose ? false : isHidden;

                drawer.classList.toggle('hidden', !shouldOpen);
                btn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
                if (icoOpen) icoOpen.classList.toggle('hidden', shouldOpen);
                if (icoClose) icoClose.classList.toggle('hidden', !shouldOpen);
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') toggleStaffNavDrawer(true);
            });

            document.addEventListener('click', function (e) {
                const header = document.getElementById('staff-main-header');
                if (header && !header.contains(e.target)) {
                    toggleStaffNavDrawer(true);
                }
            });
        </script>

        @includeIf('components.staff-reminders-banner')
    @endif

    <main class="flex-1 py-5 sm:py-6 px-3.5 sm:px-6">
        @yield('content')
    </main>

    <!-- Contact Us Centered Footer -->
    <footer class="bg-[#02338d] text-white mt-12">
        <div class="max-w-4xl mx-auto px-5 sm:px-6 py-10 text-center flex flex-col items-center justify-center space-y-4">
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

    @stack('scripts')
</body>
</html>