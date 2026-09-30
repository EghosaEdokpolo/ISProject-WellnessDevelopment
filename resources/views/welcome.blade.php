<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Ensures proper scaling and responsive rendering on mobile viewports -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WellLoop — WPMIS</title>
    <!-- Laravel Vite directive to load compiled application CSS and JS bundles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-wl-cream min-h-screen font-sans antialiased selection:bg-wl-indigo selection:text-white">

    <!-- 
      TOP ANNOUNCEMENT BAR
      Displays global system branding. Swaps between center alignment 
      on mobile devices and left alignment on small tablets or screens.
    -->
    <div class="bg-wl-navy text-wl-gold text-xs tracking-wide font-semibold px-6 py-2 text-center sm:text-left">
        WPMIS — WELLNESS PROGRAMME MANAGEMENT &amp; IMPACT SYSTEM
    </div>

    
    <nav class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
        
        <div class="flex items-center gap-3">
            <!-- Stylized Initial Box acting as a placeholder logo brand -->
            <div class="w-10 h-10 bg-wl-indigo rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-inner">
                S
            </div>
            <!-- Subsystem Title Blocks -->
            <div>
                <div class="font-bold text-wl-indigo text-base leading-tight">Strathmore University</div>
                <div class="text-xs text-gray-500 font-medium leading-tight">WellLoop · Powered by People &amp; Culture</div>
            </div>
        </div>

        <!-- Navigation Actions (Only Login is retained as Registration routes are disabled) -->
        <div>
            <a href="{{ route('login') }}" class="bg-wl-gold text-wl-navy px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide transition-all duration-200 hover:brightness-105 active:scale-95 shadow-sm">
                Log in
            </a>
        </div>
    </nav>

    <!-- 
      HERO MARKETING SECTION 
      Main textual pitch designed to catch user attention and drive them to the login terminal.
    -->
    <section class="max-w-4xl mx-auto px-6 pt-20 pb-16 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">
            Staff wellness, <span class="text-wl-indigo">measured and managed</span> in one place
        </h1>
        <p class="text-gray-600 mt-6 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
            Register for events, check in with a QR code, track your wellbeing with the
            WHO-5 index, and earn points — all in one system built for Strathmore staff.
        </p>
        <!-- Primary Call to Action Button -->
        <div class="mt-10">
            <a href="{{ route('login') }}" class="inline-block bg-wl-indigo text-white px-8 py-3.5 rounded-xl font-bold tracking-wide transition-all duration-200 hover:opacity-95 active:scale-95 shadow-md shadow-wl-indigo/10">
                Log in to WellLoop
            </a>
        </div>
    </section>

    <!-- 
      WELLNESS DIMENSIONS SHOWCASE
      Converted from actionable anchor button wrappers into structural 'div' display cards.
      These elements present system focuses statically and no longer route users to individual event channels.
    -->
    <section class="max-w-5xl mx-auto px-6 pb-20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        
        <!-- Physical Wellness Segment -->
        <div class="bg-wl-lavender rounded-2xl p-6 text-center shadow-sm border border-indigo-100/40">
            <span class="text-3xl inline-block mb-2 select-none" role="img" aria-label="Physical Dimension">🏃</span>
            <h3 class="font-bold text-wl-indigo text-base tracking-wide">Physical</h3>
            <p class="text-xs text-gray-600 mt-2 leading-relaxed">Hikes, gym orientation, active challenges</p>
        </div>

        <!-- Mental Wellness Segment -->
        <div class="bg-wl-mint rounded-2xl p-6 text-center shadow-sm border border-emerald-100/40">
            <span class="text-3xl inline-block mb-2 select-none" role="img" aria-label="Mental Dimension">🧠</span>
            <h3 class="font-bold text-emerald-800 text-base tracking-wide">Mental</h3>
            <p class="text-xs text-gray-600 mt-2 leading-relaxed">Mindfulness, stress management workshops</p>
        </div>

        <!-- Financial Wellness Segment -->
        <div class="bg-white border border-amber-100 rounded-2xl p-6 text-center shadow-sm">
            <span class="text-3xl inline-block mb-2 select-none" role="img" aria-label="Financial Dimension">💰</span>
            <h3 class="font-bold text-amber-800 text-base tracking-wide">Financial</h3>
            <p class="text-xs text-gray-600 mt-2 leading-relaxed">Budgeting forums, financial wellbeing basics</p>
        </div>

        <!-- Social Wellness Segment -->
        <div class="bg-wl-lilac rounded-2xl p-6 text-center shadow-sm border border-purple-100/40">
            <span class="text-3xl inline-block mb-2 select-none" role="img" aria-label="Social Dimension">🤝</span>
            <h3 class="font-bold text-purple-800 text-base tracking-wide">Social</h3>
            <p class="text-xs text-gray-600 mt-2 leading-relaxed">Cross-department mixers &amp; peer support</p>
        </div>

    </section>

    <!-- 
      PROCESS WORKFLOW / PIPELINE SECTION
      Breakdown steps demonstrating program rules and participant milestones.
    -->
    <section class="bg-white py-16 border-t border-gray-100">
        <div class="max-w-5xl mx-auto px-6">
            <h2 class="text-2xl font-extrabold text-gray-900 text-center mb-12 tracking-tight">How It Works</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <!-- Step 1: Initial Enrollment -->
                <div class="flex flex-col items-center">
                    <div class="w-11 h-11 bg-wl-indigo text-white rounded-full flex items-center justify-center font-bold text-base shadow-sm mb-4">1</div>
                    <h4 class="text-sm font-bold text-gray-800 tracking-wide">Register</h4>
                    <p class="text-xs text-gray-500 mt-2 max-w-[200px] leading-relaxed">Browse events and sign up in a click</p>
                </div>

                <!-- Step 2: Verification Protocol -->
                <div class="flex flex-col items-center">
                    <div class="w-11 h-11 bg-wl-indigo text-white rounded-full flex items-center justify-center font-bold text-base shadow-sm mb-4">2</div>
                    <h4 class="text-sm font-bold text-gray-800 tracking-wide">Check In</h4>
                    <p class="text-xs text-gray-500 mt-2 max-w-[200px] leading-relaxed">Scan your QR code at the event</p>
                </div>

                <!-- Step 3: Analytical Reflection -->
                <div class="flex flex-col items-center">
                    <div class="w-11 h-11 bg-wl-indigo text-white rounded-full flex items-center justify-center font-bold text-base shadow-sm mb-4">3</div>
                    <h4 class="text-sm font-bold text-gray-800 tracking-wide">Reflect</h4>
                    <p class="text-xs text-gray-500 mt-2 max-w-[200px] leading-relaxed">Quick WHO-5 wellbeing check afterward</p>
                </div>

                <!-- Step 4: Incentive Accumulation -->
                <div class="flex flex-col items-center">
                    <div class="w-11 h-11 bg-wl-gold text-wl-navy rounded-full flex items-center justify-center font-bold text-base shadow-sm mb-4">4</div>
                    <h4 class="text-sm font-bold text-gray-800 tracking-wide">Earn Points</h4>
                    <p class="text-xs text-gray-500 mt-2 max-w-[200px] leading-relaxed">Redeem for rewards, climb the leaderboard</p>
                </div>
            </div>

        </div>
    </section>

    <!-- 
      GLOBAL FOOTER 
      Legal and structural administrative signature lines.
    -->
    <footer class="text-center text-xs text-gray-400 font-medium py-10 tracking-wide border-t border-gray-50">
        WellLoop · WPMIS — Powered by People &amp; Culture, Strathmore University
    </footer>

</body>
</html>
