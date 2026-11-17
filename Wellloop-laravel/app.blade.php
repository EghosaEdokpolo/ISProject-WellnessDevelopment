<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WellLoop — WPMIS</title>

    {{--
        POINTER: this uses the Tailwind "Play CDN" — zero build step, great
        for learning and demos. Once the app is further along, swap this for
        a proper Vite + Tailwind install (`npm install`, tailwind.config.js)
        so the CSS gets purged/optimised for production. Same class names,
        same colours — just faster and smaller in production.
    --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // POINTER: naming these 'wl-*' (WellLoop) instead of
                        // reusing Tailwind's built-in 'indigo-500' etc. means
                        // if the brand palette shifts later, you edit these
                        // six lines instead of hunting through every view.
                        'wl-navy':     '#161a33',
                        'wl-indigo':   '#5b5fc7',
                        'wl-gold':     '#c99a2e',
                        'wl-cream':    '#f6f4ee',
                        'wl-lavender': '#e4e2fb',
                        'wl-mint':     '#dcf3e4',
                        'wl-lilac':    '#f1e4fa',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-wl-cream min-h-screen">

    {{-- Top identity bar --}}
    <div class="bg-wl-navy text-wl-gold text-xs tracking-wide font-semibold px-6 py-2 flex justify-between items-center">
        <span>WPMIS — WELLNESS PROGRAMME MANAGEMENT &amp; IMPACT SYSTEM</span>
        {{-- POINTER: this used to hardcode BOTH pills every time, with HR
             always highlighted gold — misleading if a staff member was
             actually logged in. Now it checks the real user and only shows
             (and highlights) whichever role they actually are. --}}
        @auth
            <span class="{{ auth()->user()->isHrCoordinator() ? 'bg-wl-gold text-wl-navy font-bold' : 'bg-white/10 text-white' }} rounded-full px-3 py-1">
                {{ auth()->user()->isHrCoordinator() ? 'HR Coordinator' : 'Staff Member' }}
            </span>
        @endauth
    </div>

    {{-- Main nav --}}
    <nav class="bg-white shadow-sm px-6 py-3 flex items-center gap-8">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 bg-wl-indigo rounded-lg flex items-center justify-center text-white font-bold">S</div>
            <div>
                <div class="font-bold text-wl-indigo leading-tight">Strathmore University</div>
                <div class="text-xs text-gray-500 leading-tight">WellLoop · Powered by People &amp; Culture</div>
            </div>
        </div>

        <div class="flex gap-6 text-sm font-medium text-gray-600 items-center">
            @auth
                @if(auth()->user()->isHrCoordinator())
                    {{-- HR Coordinator nav --}}
                    <a href="{{ route('dashboard.hr') }}" class="hover:text-wl-indigo">Dashboard</a>
                    <a href="{{ route('attendees.index') }}" class="hover:text-wl-indigo">Attendees</a>
                    <a href="{{ route('events.create') }}" class="hover:text-wl-indigo">Manage Events</a>
                    <a href="{{ route('events.index') }}" class="hover:text-wl-indigo">Events</a>
                @else
                    {{-- Staff nav --}}
                    <a href="{{ route('events.index') }}" class="hover:text-wl-indigo">Events</a>
                    <a href="{{ route('rewards.index') }}" class="hover:text-wl-indigo">Rewards</a>
                @endif
                <span class="text-xs text-gray-400 ml-2">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-xs text-gray-500 hover:text-red-600">Log out</button>
                </form>
            @else
                <a href="{{ route('events.index') }}" class="hover:text-wl-indigo">Events</a>
                <a href="{{ route('login') }}" class="hover:text-wl-indigo">Log in</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-8">
        @if(session('status'))
            <div class="mb-6 bg-wl-mint text-emerald-800 px-4 py-3 rounded-lg text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>

</body>
</html>
