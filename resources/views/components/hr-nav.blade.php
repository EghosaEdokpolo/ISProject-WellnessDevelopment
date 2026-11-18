<!-- Top Institutional HR Module Bar -->
<div class="bg-[#02338d] text-white px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 text-xs font-bold tracking-wide shadow-sm">
    <div class="flex items-center gap-3">
        <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" class="h-5 w-5 object-contain" alt="Strathmore" onerror="this.style.display='none'">
        <span class="font-wordmark tracking-wider">WPMIS — STRATHMORE UNIVERSITY PEOPLE & CULTURE</span>
    </div>

    <div class="flex items-center gap-3">
        <span class="bg-[#c99a2e] text-[#01225e] px-3 py-1 rounded-full font-extrabold text-[11px]">HR Coordinator</span>
        @auth
            <span class="hidden sm:inline text-blue-100 font-semibold pl-3 border-l border-white/20">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        title="Sign out of WPMIS"
                        class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-[#e03c31] text-white border border-white/20 hover:border-[#e03c31] px-3.5 py-1 rounded-full text-[11px] font-extrabold transition-all duration-150 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Sign Out</span>
                </button>
            </form>
        @endauth
    </div>
</div>

<!-- HR Sub-Navigation -->
<header class="bg-white border-b border-slate-200 px-6 py-2 sticky top-0 z-50">
    <nav class="flex items-center gap-1 max-w-7xl mx-auto overflow-x-auto whitespace-nowrap scrollbar-hide py-1">
        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mr-4">Dashboards</span>

        <a href="{{ route('dashboard.hr') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-extrabold transition-colors {{ request()->routeIs('dashboard.hr') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Overview</a>
        <a href="{{ route('impact.analytics') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-extrabold transition-colors {{ request()->routeIs('impact.analytics') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Impact</a>
        <a href="{{ route('retention.metrics') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-extrabold transition-colors {{ request()->routeIs('retention.metrics') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Retention</a>
        <a href="{{ route('department.analysis') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-extrabold transition-colors {{ request()->routeIs('department.analysis') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Departmental</a>

        <div class="h-4 w-px bg-slate-200 mx-2"></div>

        <a href="{{ route('attendees.index') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-extrabold transition-colors {{ request()->routeIs('attendees.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Attendees</a>
        <a href="{{ route('events.management') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-extrabold transition-colors {{ request()->routeIs('events.management', 'events.create', 'events.edit', 'events.qr', 'events.qr.poster', 'checkin.scan') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Events</a>
        <a href="{{ route('settings.edit') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-xs font-extrabold transition-colors {{ request()->routeIs('settings.*') ? 'bg-[#02338d] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">Settings</a>
    </nav>
</header>