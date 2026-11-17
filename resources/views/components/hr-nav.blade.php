<style>
    @import url('https://fonts.cdnfonts.com/css/sansation');
    body, h1, h2, h3, h4, h5, h6, p, div, span, a, button, input, select, textarea, table, th, td, label { 
        font-family: 'Sansation', sans-serif !important; 
    }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<!-- Top Institutional HR Module Bar -->
<div class="bg-[#02338d] text-white px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 text-xs font-bold tracking-wide shadow-sm">
    <div class="flex items-center gap-3">
        <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}?v=2" class="h-5 w-5 object-contain" alt="Strathmore" onerror="this.style.display='none'">
        <span>WPMIS — STRATHMORE UNIVERSITY PEOPLE & CULTURE</span>
    </div>

    <div class="flex items-center gap-2.5">
        <span class="bg-[#c99a2e] text-[#01225e] px-3 py-0.5 rounded-full font-extrabold">HR Coordinator</span>
        @auth
            <span class="hidden sm:inline text-blue-200 pl-2 border-l border-white/20">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="bg-red-600/80 hover:bg-red-600 text-white px-2.5 py-0.5 rounded-full transition">
                    Logout
                </button>
            </form>
        @endauth
    </div>
</div>

<!-- HR Sub-Navigation -->
<header class="bg-white border-b border-gray-200 px-6 py-2 sticky top-0 z-50">
    <nav class="flex items-center gap-1 max-w-7xl mx-auto overflow-x-auto whitespace-nowrap scrollbar-hide py-1">
        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-4">Dashboards</span>

        <a href="{{ route('dashboard.hr') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('dashboard.hr') ? 'bg-[#c99a2e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Overview</a>
        <a href="{{ route('impact.analytics') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('impact.analytics') ? 'bg-[#c99a2e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Impact</a>
        <a href="{{ route('retention.metrics') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('retention.metrics') ? 'bg-[#c99a2e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Retention</a>
        <a href="{{ route('department.analysis') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('department.analysis') ? 'bg-[#c99a2e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Departmental</a>
        
        <div class="h-4 w-px bg-gray-300 mx-2"></div>
        
        <a href="{{ route('attendees.index') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('attendees.*') ? 'bg-[#c99a2e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Attendees</a>
        <a href="{{ route('events.management') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('events.management', 'events.create', 'events.edit', 'events.qr') ? 'bg-[#c99a2e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Events</a>
        <a href="{{ route('settings.edit') }}" class="flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('settings.*') ? 'bg-[#c99a2e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Settings</a>
    </nav>
</header>