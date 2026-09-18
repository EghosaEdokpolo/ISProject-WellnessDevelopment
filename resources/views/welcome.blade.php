<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WellLoop — WPMIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                'wl-navy':'#161a33','wl-indigo':'#5b5fc7','wl-gold':'#c99a2e',
                'wl-cream':'#f6f4ee','wl-lavender':'#e4e2fb','wl-mint':'#dcf3e4','wl-lilac':'#f1e4fa'
            }}}
        }
    </script>
</head>
<body class="bg-wl-cream min-h-screen">

    <div class="bg-wl-navy text-wl-gold text-xs tracking-wide font-semibold px-6 py-2">
        WPMIS — WELLNESS PROGRAMME MANAGEMENT &amp; IMPACT SYSTEM
    </div>

    <nav class="bg-white shadow-sm px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 bg-wl-indigo rounded-lg flex items-center justify-center text-white font-bold">S</div>
            <div>
                <div class="font-bold text-wl-indigo leading-tight">Strathmore University</div>
                <div class="text-xs text-gray-500 leading-tight">WellLoop · Powered by People &amp; Culture</div>
            </div>
        </div>
        <div class="flex gap-4 text-sm font-medium">
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-wl-indigo px-3 py-2">Log in</a>
            <a href="{{ route('register') }}" class="bg-wl-gold text-wl-navy px-4 py-2 rounded-lg font-semibold">Get started</a>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="max-w-5xl mx-auto px-6 pt-16 pb-12 text-center">
        <h1 class="text-4xl font-bold text-gray-900 leading-tight">
            Staff wellness, <span class="text-wl-indigo">measured and managed</span> in one place
        </h1>
        <p class="text-gray-500 mt-4 max-w-2xl mx-auto">
            Register for events, check in with a QR code, track your wellbeing with the
            WHO-5 index, and earn points — all in one system built for Strathmore staff.
        </p>
        <div class="flex justify-center gap-4 mt-8">
            <a href="{{ route('login') }}" class="bg-wl-indigo text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90">
                Log in
            </a>
            <a href="{{ route('register') }}" class="bg-white border border-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:border-wl-indigo">
                Create an account
            </a>
        </div>
    </section>

    {{-- Wellness dimension cards — each links straight into the filtered
         Events list. A logged-out visitor gets sent to login first (the
         'auth' middleware on /events catches them, then Laravel's Breeze
         redirects back to the originally-requested URL after they sign in —
         so they land exactly on the filtered category they clicked, not a
         generic events page). --}}
    <section class="max-w-5xl mx-auto px-6 pb-16 grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('events.index', ['category' => 'physical']) }}"
           class="bg-wl-lavender rounded-xl p-5 text-center hover:ring-2 hover:ring-wl-indigo transition">
            <p class="text-2xl mb-1">🏃</p>
            <p class="font-semibold text-wl-indigo text-sm">Physical</p>
            <p class="text-xs text-gray-500 mt-1">Hikes, gym orientation, active challenges</p>
        </a>
        <a href="{{ route('events.index', ['category' => 'mental']) }}"
           class="bg-wl-mint rounded-xl p-5 text-center hover:ring-2 hover:ring-emerald-500 transition">
            <p class="text-2xl mb-1">🧠</p>
            <p class="font-semibold text-emerald-700 text-sm">Mental</p>
            <p class="text-xs text-gray-500 mt-1">Mindfulness, stress management workshops</p>
        </a>
        <a href="{{ route('events.index', ['category' => 'financial']) }}"
           class="bg-wl-cream border border-amber-100 rounded-xl p-5 text-center hover:ring-2 hover:ring-amber-500 transition">
            <p class="text-2xl mb-1">💰</p>
            <p class="font-semibold text-amber-700 text-sm">Financial</p>
            <p class="text-xs text-gray-500 mt-1">Budgeting forums, financial wellbeing basics</p>
        </a>
        <a href="{{ route('events.index', ['category' => 'social']) }}"
           class="bg-wl-lilac rounded-xl p-5 text-center hover:ring-2 hover:ring-purple-500 transition">
            <p class="text-2xl mb-1">🤝</p>
            <p class="font-semibold text-purple-700 text-sm">Social</p>
            <p class="text-xs text-gray-500 mt-1">Cross-department mixers &amp; peer support</p>
        </a>
    </section>

    {{-- How it works --}}
    <section class="bg-white py-14">
        <div class="max-w-5xl mx-auto px-6">
            <h2 class="text-xl font-bold text-gray-900 text-center mb-10">How it works</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="w-10 h-10 bg-wl-indigo text-white rounded-full flex items-center justify-center mx-auto font-bold mb-3">1</div>
                    <p class="text-sm font-semibold text-gray-800">Register</p>
                    <p class="text-xs text-gray-500 mt-1">Browse events and sign up in a click</p>
                </div>
                <div>
                    <div class="w-10 h-10 bg-wl-indigo text-white rounded-full flex items-center justify-center mx-auto font-bold mb-3">2</div>
                    <p class="text-sm font-semibold text-gray-800">Check in</p>
                    <p class="text-xs text-gray-500 mt-1">Scan your QR code at the event</p>
                </div>
                <div>
                    <div class="w-10 h-10 bg-wl-indigo text-white rounded-full flex items-center justify-center mx-auto font-bold mb-3">3</div>
                    <p class="text-sm font-semibold text-gray-800">Reflect</p>
                    <p class="text-xs text-gray-500 mt-1">Quick WHO-5 wellbeing check afterward</p>
                </div>
                <div>
                    <div class="w-10 h-10 bg-wl-gold text-wl-navy rounded-full flex items-center justify-center mx-auto font-bold mb-3">4</div>
                    <p class="text-sm font-semibold text-gray-800">Earn points</p>
                    <p class="text-xs text-gray-500 mt-1">Redeem for rewards, climb the leaderboard</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center text-xs text-gray-400 py-8">
        WellLoop · WPMIS — Powered by People &amp; Culture, Strathmore University
    </footer>

</body>
</html>
