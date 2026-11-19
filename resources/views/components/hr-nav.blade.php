<!-- Unified HR Header (Strathmore Blue — Safe Down to 320px Phone & Half-Screen Laptop) -->
<header id="hr-main-header" class="bg-[#02338d] border-b border-[#01225e] px-3 sm:px-6 py-3 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-2 sm:gap-4">

        <!-- Left: Strathmore University Branding -->
        <a href="{{ route('dashboard.hr') }}" class="flex items-center gap-2.5 sm:gap-3 min-w-0">
            <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" class="h-8 w-8 sm:h-9 sm:w-9 object-contain shrink-0" alt="Strathmore" onerror="this.style.display='none'">
            <div class="leading-tight min-w-0">
                <span class="block text-xs sm:text-sm font-extrabold text-white font-wordmark truncate">Strathmore University</span>
                <span class="block text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-blue-200 truncate">WellLoop • People & Culture</span>
            </div>
        </a>

        <!-- Center: Full Desktop HR Navigation (1280px+) -->
        <nav class="hidden xl:flex items-center gap-1 whitespace-nowrap">
            <a href="{{ route('dashboard.hr') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('dashboard.hr') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Overview</a>
            <a href="{{ route('impact.analytics') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('impact.analytics') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Impact</a>
            <a href="{{ route('retention.metrics') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('retention.metrics') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Retention</a>
            <a href="{{ route('department.analysis') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('department.analysis') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Departmental</a>

            <div class="h-4 w-px bg-white/20 mx-1.5 shrink-0"></div>

            <a href="{{ route('attendees.index') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('attendees.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Attendees</a>
            <a href="{{ route('events.management') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('events.management', 'events.create', 'events.edit', 'events.qr', 'events.qr.poster', 'checkin.scan') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Events</a>
            <a href="{{ route('settings.edit') }}" class="px-3 py-2 rounded-xl text-xs font-extrabold transition-colors {{ request()->routeIs('settings.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">Settings</a>
        </nav>

        <!-- Right: Desktop HR Badge, User Name & Sign Out (1280px+) -->
        @auth
            <div class="hidden xl:flex items-center gap-3 border-l border-white/20 pl-4 shrink-0">
                <span class="text-[11px] font-extrabold text-[#01225e] bg-[#c99a2e] px-2.5 py-1 rounded-full">
                    HR Coordinator
                </span>
                <span class="text-xs font-extrabold text-white">
                    {{ auth()->user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                            title="Sign out of WPMIS"
                            class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-[#e03c31] text-white border border-white/20 hover:border-[#e03c31] px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all duration-150">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        @endauth

        <!-- Compact / Minimized / Mobile Toggle Button (Below 1280px) -->
        <div class="flex xl:hidden items-center gap-2 shrink-0">
            <button type="button"
                    id="hr-drawer-toggle"
                    aria-expanded="false"
                    aria-controls="hr-responsive-drawer"
                    onclick="toggleHrNavDrawer()"
                    class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 px-2.5 sm:px-3 py-2 rounded-xl text-xs font-extrabold transition">
                <svg id="hr-icon-open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg id="hr-icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="hidden sm:inline">Menu</span>
            </button>
        </div>

    </div>

    <!-- Collapsible Responsive Drawer (All 7 HR Pages Always Accessible) -->
    <div id="hr-responsive-drawer" class="hidden xl:hidden max-w-7xl mx-auto pt-4 pb-2 mt-3 border-t border-white/15 space-y-4">
        <div>
            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-blue-200/80 mb-2 px-1">
                Analytics Dashboards
            </span>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <a href="{{ route('dashboard.hr') }}" class="px-3 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('dashboard.hr') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Overview</a>
                <a href="{{ route('impact.analytics') }}" class="px-3 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('impact.analytics') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Impact</a>
                <a href="{{ route('retention.metrics') }}" class="px-3 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('retention.metrics') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Retention</a>
                <a href="{{ route('department.analysis') }}" class="px-3 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('department.analysis') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Departmental</a>
            </div>
        </div>

        <div>
            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-blue-200/80 mb-2 px-1">
                Operations & Compliance
            </span>
            <div class="grid grid-cols-3 gap-2">
                <a href="{{ route('attendees.index') }}" class="px-3 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('attendees.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Attendees</a>
                <a href="{{ route('events.management') }}" class="px-3 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('events.management', 'events.create', 'events.edit', 'events.qr', 'events.qr.poster', 'checkin.scan') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Events</a>
                <a href="{{ route('settings.edit') }}" class="px-3 py-2.5 rounded-xl text-xs font-extrabold text-center transition {{ request()->routeIs('settings.*') ? 'bg-white text-[#02338d] shadow-sm' : 'text-blue-100 bg-white/10 hover:bg-white/15' }}">Settings</a>
            </div>
        </div>

        @auth
            <div class="pt-3 border-t border-white/15 flex flex-wrap items-center justify-between gap-3 px-1">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-[10px] font-extrabold text-[#01225e] bg-[#c99a2e] px-2.5 py-1 rounded-full shrink-0">
                        HR Coordinator
                    </span>
                    <span class="text-xs font-extrabold text-white truncate">
                        {{ auth()->user()->name }}
                    </span>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-[#e03c31] hover:bg-red-700 text-white px-3.5 py-2 rounded-xl text-xs font-extrabold transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        @endauth
    </div>
</header>

<script>
    function toggleHrNavDrawer(forceClose = false) {
        const drawer   = document.getElementById('hr-responsive-drawer');
        const btn      = document.getElementById('hr-drawer-toggle');
        const icoOpen  = document.getElementById('hr-icon-open');
        const icoClose = document.getElementById('hr-icon-close');
        if (!drawer || !btn) return;

        const isHidden   = drawer.classList.contains('hidden');
        const shouldOpen = forceClose ? false : isHidden;

        drawer.classList.toggle('hidden', !shouldOpen);
        btn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
        if (icoOpen) icoOpen.classList.toggle('hidden', shouldOpen);
        if (icoClose) icoClose.classList.toggle('hidden', !shouldOpen);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') toggleHrNavDrawer(true);
    });

    document.addEventListener('click', function (e) {
        const header = document.getElementById('hr-main-header');
        if (header && !header.contains(e.target)) {
            toggleHrNavDrawer(true);
        }
    });
</script>