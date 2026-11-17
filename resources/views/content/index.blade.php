@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12">

    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Wellness Content Hub</h1>
            <p class="text-sm text-slate-500 mt-2">Explore articles, guides, and resources across all 8 wellness dimensions.</p>
        </div>

        <form method="GET" action="{{ route('content.index') }}" class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm w-full md:w-auto">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search articles..."
                   class="border-none bg-transparent text-sm px-3 py-2 w-full md:w-64 focus:ring-0 outline-none">
            <button type="submit" class="bg-slate-900 text-white font-bold px-5 py-2 rounded-lg text-xs hover:bg-brand transition">Search</button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-8 bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl text-sm font-bold border border-emerald-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Category Pills -->
    <div class="flex flex-wrap gap-2 mb-10 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm w-fit">
        <a href="{{ route('content.index') }}" class="px-5 py-2 rounded-xl text-xs font-bold transition-colors {{ !$activeCategory ? 'bg-brand text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">All</a>
        @foreach(['physical','mental','financial','social','intellectual','occupational','environmental','spiritual'] as $cat)
            <a href="{{ route('content.index', ['category' => $cat]) }}"
               class="px-5 py-2 rounded-xl text-xs font-bold capitalize transition-colors {{ $activeCategory === $cat ? 'bg-brand text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Content Grid -->
    @if($contents->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-16 text-center shadow-sm">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">No articles found</h3>
            <p class="text-slate-500 text-sm">Try adjusting your search or category filter.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($contents as $content)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    @if($content->image_url)
                        <div class="h-48 w-full overflow-hidden">
                            <img src="{{ $content->image_url }}" alt="{{ $content->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    @else
                        <div class="h-2 bg-brand"></div>
                    @endif

                    <div class="p-6 flex flex-col flex-1">
                        @if($content->category)
                            <span class="text-[10px] font-bold uppercase tracking-wider text-brand bg-brand-light px-3 py-1.5 rounded-full w-fit mb-4">
                                {{ $content->category }}
                            </span>
                        @endif

                        <h3 class="font-bold text-xl text-slate-900 mb-3 leading-tight group-hover:text-brand transition-colors">
                            <a href="{{ route('content.show', $content->slug) }}" class="focus:outline-none">
                                {{ $content->title }}
                            </a>
                        </h3>
                        
                        <p class="text-sm text-slate-500 mb-6 line-clamp-2">{{ $content->excerpt }}</p>

                        <div class="mt-auto flex items-center justify-between pt-5 border-t border-slate-100">
                            <a href="{{ route('content.show', $content->slug) }}"
                               class="text-brand font-bold text-sm hover:underline flex items-center gap-1">
                                Read Article <span aria-hidden="true">&rarr;</span>
                            </a>

                            <form method="POST" action="{{ route('content.save', $content->slug) }}">
                                @csrf
                                <button type="submit" title="{{ in_array($content->id, $savedIds) ? 'Remove from saved' : 'Save for later' }}"
                                        class="p-2 rounded-xl transition-colors {{ in_array($content->id, $savedIds) ? 'bg-brand-light text-brand' : 'bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-brand' }}">
                                    <svg class="w-5 h-5" fill="{{ in_array($content->id, $savedIds) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 flex justify-center">
            {{ $contents->links() }}
        </div>
    @endif
</div>
@endsection