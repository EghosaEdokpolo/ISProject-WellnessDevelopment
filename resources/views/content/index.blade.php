@extends('layouts.app')

@section('content')
    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Wellness Content Hub</h1>
            <p class="text-sm text-gray-500 mt-1">Guides and resources to support your wellbeing journey.</p>
        </div>

        <form method="GET" action="{{ route('content.index') }}" class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..." class="w-full border-gray-200 rounded-lg text-sm pl-10 py-2.5 focus:ring-wl-indigo focus:border-wl-indigo shadow-sm">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <select name="category" class="border-gray-200 rounded-lg text-sm py-2.5 shadow-sm focus:ring-wl-indigo focus:border-wl-indigo" onchange="this.form.submit()">
                <option value="All">All Categories</option>
                <option value="Physical" {{ request('category') == 'Physical' ? 'selected' : '' }}>Physical</option>
                <option value="Mental" {{ request('category') == 'Mental' ? 'selected' : '' }}>Mental</option>
                <option value="Financial" {{ request('category') == 'Financial' ? 'selected' : '' }}>Financial</option>
                <option value="Social" {{ request('category') == 'Social' ? 'selected' : '' }}>Social</option>
            </select>
        </form>
    </div>

    @if(count($articles) === 0)
        <div class="bg-white rounded-2xl p-16 text-center border border-gray-100 shadow-sm">
            <div class="text-5xl mb-4">🔍</div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">No articles found</h3>
            <p class="text-gray-500 text-sm">Try adjusting your search or category filter.</p>
            <a href="{{ route('content.index') }}" class="inline-block mt-4 text-wl-indigo font-semibold hover:underline">Clear filters</a>
        </div>
    @else
        <!-- Featured Article (if any) -->
        @php $featured = collect($articles)->firstWhere('featured', true); @endphp
        @if($featured && !request('search') && !request('category'))
            <div class="mb-10">
                <a href="{{ route('content.show', array_search($featured, $articles)) }}" class="bg-gradient-to-r from-wl-navy to-gray-800 rounded-2xl p-8 md:p-10 text-white shadow-lg flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden group">
                    <div class="relative z-10 max-w-xl">
                        <span class="inline-block bg-wl-gold text-wl-navy text-[10px] font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">Featured Guide</span>
                        <h2 class="text-2xl md:text-3xl font-bold mb-3 group-hover:text-wl-gold transition-colors">{{ $featured['title'] }}</h2>
                        <p class="text-gray-300 text-sm md:text-base mb-6">{{ $featured['desc'] }}</p>
                        <span class="inline-flex items-center gap-2 text-sm font-bold text-wl-gold">
                            Read Article <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                    </div>
                    <div class="text-8xl md:text-9xl opacity-20 absolute -right-4 -bottom-8 transform rotate-12 group-hover:scale-110 transition-transform">{{ $featured['emoji'] }}</div>
                </a>
            </div>
        @endif

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $slug => $article)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col hover:shadow-lg hover:-translate-y-1 transition-all duration-200 relative group">
                    
                    <!-- Bookmark -->
                    <form method="POST" action="{{ route('content.save', $slug) }}" class="absolute top-5 right-5 z-10">
                        @csrf
                        <button type="submit" class="p-2 bg-white/80 backdrop-blur-sm rounded-full text-gray-400 hover:text-wl-gold transition-colors shadow-sm {{ in_array($slug, $savedSlugs) ? 'text-wl-gold bg-wl-gold/10' : '' }}">
                            <svg class="w-5 h-5" fill="{{ in_array($slug, $savedSlugs) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                        </button>
                    </form>

                    <a href="{{ route('content.show', $slug) }}" class="p-6 flex flex-col h-full">
                        <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-3xl mb-5 shadow-inner">{{ $article['emoji'] }}</div>
                        
                        <span class="inline-block {{ $article['color'] }} text-[10px] font-bold px-2.5 py-1 rounded-md mb-3 w-fit">{{ $article['category'] }}</span>
                        
                        <h3 class="font-bold text-gray-900 text-lg mb-2 leading-tight group-hover:text-wl-indigo transition-colors">{{ $article['title'] }}</h3>
                        <p class="text-sm text-gray-500 mb-6 line-clamp-2">{{ $article['desc'] }}</p>
                        
                        <div class="mt-auto flex items-center justify-between text-xs font-bold text-gray-400">
                            <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> 3 min read</span>
                            <span class="text-wl-indigo group-hover:underline">Read &rarr;</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
@endsection