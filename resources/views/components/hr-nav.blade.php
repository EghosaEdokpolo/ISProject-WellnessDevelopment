<div class="bg-brand text-white px-6 py-2.5 flex items-center gap-3 text-xs font-semibold tracking-wide shadow-sm">
    <img src="{{ asset('assets/Strathmore_uni_logo-notext.png') }}" class="h-5"> 
    WPMIS — WELLNESS PROGRAMME MANAGEMENT
</div>

<header class="bg-white border-b border-gray-200 px-6 py-2 sticky top-0 z-50">
    <nav class="flex items-center gap-1 max-w-7xl mx-auto overflow-x-auto">
        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-4">Dashboards</span>
        
        <!-- Overview -->
        <a href="{{ route('dashboard.hr') }}" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('dashboard.hr') ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Overview</a>
        
        <!-- Impact -->
        <a href="{{ route('impact.analytics') }}" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('impact.analytics') ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Impact</a>
        
        <!-- Retention -->
        <a href="{{ route('retention.metrics') }}" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('retention.metrics') ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Retention</a>
        
        <!-- Departmental -->
        <a href="{{ route('department.analysis') }}" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('department.analysis') ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Departmental</a>
        
        <div class="h-4 w-px bg-gray-300 mx-2"></div>
        
        <!-- Attendees -->
        <a href="{{ route('attendees.index') }}" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('attendees.*') ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Attendees</a>
        
        <!-- Events -->
        <a href="{{ route('events.management') }}" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('events.*') ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Events</a>
        
        <!-- Users -->
        <a href="{{ route('hr.users.index') }}" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('hr.users.*') ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Users</a>
        
        <!-- Settings -->
        <a href="{{ route('settings.edit') }}" class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('settings.*') ? 'bg-brand text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">Settings</a>
    </nav>
</header>