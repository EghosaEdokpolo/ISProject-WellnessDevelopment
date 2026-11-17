<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'WPMIS — Strathmore University') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2">
    
    <!-- Official Strathmore Font: Sansation -->
    <link href="https://fonts.cdnfonts.com/css/sansation" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    colors: { 
                        brand: '#02338d', 
                        'brand-dark': '#01225e',
                        'brand-light': '#e8f0fe', 
                        wl: { 
                            gold: '#c99a2e',
                            red: '#cc0000'
                        } 
                    },
                    fontFamily: { sans: ['Sansation', 'sans-serif'] }
                } 
            } 
        }
    </script>
    
    <style>
        /* Strict Strathmore Brand Typography Enforcement */
        body, h1, h2, h3, h4, h5, h6, p, div, span, a, button, input, select, textarea, table, th, td, label { 
            font-family: 'Sansation', sans-serif !important; 
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col">

    <!-- Top Institutional Module Bar -->
    <div class="bg-[#01225e] text-white px-6 py-2 flex flex-wrap items-center justify-between gap-2 text-xs tracking-wider font-bold">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#c99a2e]"></span>
            <span>WPMIS — STRATHMORE UNIVERSITY WELLNESS MODULE</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="bg-[#c99a2e] text-[#01225e] px-3 py-0.5 rounded-full font-extrabold">Staff Portal</span>
        </div>
    </div>

    <!-- Automated Reminder Strip (For Staff & HR) -->
    @php
        $upcomingReminderEvent = null;
        try {
            if (auth()->check()) {
                if (auth()->user()->isHrCoordinator()) {
                    $upcomingReminderEvent = \App\Models\Event::where('starts_at', '>=', now())
                        ->where('starts_at', '<=', now()->addDays(3))
                        ->orderBy('starts_at', 'asc')
                        ->first();
                } else {
                    $upcomingReminderEvent = \App\Models\Event::whereHas('registrations', function ($q) {
                            $q->where('user_id', auth()->id());
                        })
                        ->where('starts_at', '>=', now())
                        ->where('starts_at', '<=', now()->addDays(3))
                        ->orderBy('starts_at', 'asc')
                        ->first();
                }
            }
        } catch (\Throwable $e) {
            $upcomingReminderEvent = null;
        }
    @endphp

    @if($upcomingReminderEvent)
        <div class="bg-amber-50 border-b border-amber-200 px-6 py-2.5 text-xs text-amber-900">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2 font-bold">
                    <svg class="w-4 h-4 text-[#c99a2e] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span>
                        {{ auth()->user()->isHrCoordinator() ? 'HR Reminder:' : 'Upcoming Event Reminder:' }}
                        <strong>{{ $upcomingReminderEvent->name }}</strong> starts {{ $upcomingReminderEvent->starts_at->diffForHumans() }}
                        ({{ $upcomingReminderEvent->starts_at->format('D, d M \a\t H:i') }} at {{ $upcomingReminderEvent->location }}).
                    </span>
                </div>
                <a href="{{ auth()->user()->isHrCoordinator() ? route('checkin.scan', $upcomingReminderEvent) : route('checkin.index') }}"
                   class="text-[#02338d] font-extrabold underline hover:text-[#01225e]">
                    {{ auth()->user()->isHrCoordinator() ? 'Open Venue Check-In' : 'View Check-In Details' }} →
                </a>
            </div>
        </div>
    @endif

    <!-- Main Navigation Bar (Official Strathmore University Brand Palette) -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-3.5 flex items-center justify-between">
            
            <!-- Official Strathmore Crest & Title -->
            <div class="flex items-center gap-3.5">
                <a href="{{ route('events.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2"
                         alt="Strathmore University"
                         class="w-10 h-10 object-contain"
                         onerror="this.style.display='none'">
                    <div>
                        <h1 class="text-lg font-extrabold text-[#02338d] leading-tight tracking-tight">Strathmore University</h1>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">WellLoop · People & Culture</p>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center gap-2">
                <a href="{{ route('events.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold transition-colors flex items-center gap-2 {{ request()->routeIs('events.*', 'registrations.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-[#02338d]' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Events
                </a>
                <a href="{{ route('checkin.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold transition-colors flex items-center gap-2 {{ request()->routeIs('checkin.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-[#02338d]' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Check-in
                </a>
                <a href="{{ route('feedback.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold transition-colors flex items-center gap-2 {{ request()->routeIs('feedback.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-[#02338d]' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Feedback
                </a>
                <a href="{{ route('rewards.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold transition-colors flex items-center gap-2 {{ request()->routeIs('rewards.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-[#02338d]' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    Rewards
                </a>
                <a href="{{ route('content.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold transition-colors flex items-center gap-2 {{ request()->routeIs('content.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-[#02338d]' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Content Hub
                </a>
            </nav>

            <!-- User Profile -->
            @auth
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 border-l border-slate-200 pl-4">
                        <a href="{{ route('profile.edit') }}" class="text-sm font-bold text-slate-700 hover:text-[#02338d] transition">
                            {{ auth()->user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-slate-400 hover:text-red-600 font-bold transition">Logout</button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Mobile Scrollable Navigation Bar (< 1024px) -->
        <nav class="flex lg:hidden items-center gap-1.5 px-6 py-2 bg-slate-50 border-t border-slate-200 overflow-x-auto whitespace-nowrap scrollbar-hide">
            <a href="{{ route('events.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('events.*', 'registrations.*') ? 'bg-[#02338d] text-white' : 'text-slate-600 bg-white border border-slate-200' }}">Events</a>
            <a href="{{ route('checkin.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('checkin.*') ? 'bg-[#02338d] text-white' : 'text-slate-600 bg-white border border-slate-200' }}">Check-in</a>
            <a href="{{ route('feedback.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('feedback.*') ? 'bg-[#02338d] text-white' : 'text-slate-600 bg-white border border-slate-200' }}">Feedback</a>
            <a href="{{ route('rewards.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('rewards.*') ? 'bg-[#02338d] text-white' : 'text-slate-600 bg-white border border-slate-200' }}">Rewards</a>
            <a href="{{ route('content.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('content.*') ? 'bg-[#02338d] text-white' : 'text-slate-600 bg-white border border-slate-200' }}">Content Hub</a>
        </nav>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>