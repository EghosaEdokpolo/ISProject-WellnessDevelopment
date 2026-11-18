@extends('layouts.app')

@section('content')
@php
    $categories = [
        'all'           => 'All',
        'saved'         => 'Saved (' . ($savedCount ?? count($bookmarkedIds ?? [])) . ')',
        'physical'      => 'Physical',
        'mental'        => 'Mental',
        'financial'     => 'Financial',
        'social'        => 'Social',
        'intellectual'  => 'Intellectual',
        'occupational'  => 'Occupational',
        'environmental' => 'Environmental',
        'spiritual'     => 'Spiritual',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header & Search Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Wellness Content Hub</h1>
            <p class="text-xs text-slate-500 italic mt-0.5">
                Explore articles, guides, and resources across all 8 wellness dimensions.
            </p>
        </div>

        <form method="GET" action="{{ route('content.index') }}" class="flex items-center gap-2">
            @if(($activeCategory ?? 'all') !== 'all')
                <input type="hidden" name="category" value="{{ $activeCategory }}">
            @endif
            <input type="text"
                   name="search"
                   value="{{ $search ?? '' }}"
                   placeholder="Search articles..."
                   class="border border-slate-200 rounded-xl text-xs font-bold px-4 py-2.5 bg-white w-60 focus:ring-[#02338d] focus:border-[#02338d]">
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold px-4 py-2.5 rounded-xl transition">
                Search
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-extrabold flex items-center gap-2">
            <span>✓ {{ session('success') }}</span>
            @if(($activeCategory ?? 'all') !== 'saved')
                <a href="{{ route('content.index', ['category' => 'saved']) }}" class="underline text-[#02338d] ml-2">
                    View Saved Articles →
                </a>
            @endif
        </div>
    @endif

    <!-- Category + Saved Filter Pills -->
    <div class="flex flex-wrap items-center gap-2">
        @foreach($categories as $key => $label)
            @php $isActive = ($activeCategory ?? 'all') === $key; @endphp
            <a href="{{ route('content.index', array_filter(['category' => $key === 'all' ? null : $key, 'search' => $search ?? null])) }}"
               class="text-xs font-extrabold px-4 py-2 rounded-full transition {{ $isActive ? 'bg-[#02338d] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Articles Grid -->
    @if($articles->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-10 text-center">
            <p class="text-sm font-extrabold text-slate-700">
                {{ ($activeCategory ?? 'all') === 'saved' ? 'No saved articles yet.' : 'No articles match your filter.' }}
            </p>
            <p class="text-xs text-slate-500 mt-1">
                {{ ($activeCategory ?? 'all') === 'saved' ? 'Click the bookmark icon on any wellness guide to save it here for later reading.' : 'Try clearing your search or selecting another wellness dimension.' }}
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $article)
                @php $isSaved = in_array($article->id, $bookmarkedIds ?? [], true); @endphp
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                    <div class="h-1.5 bg-[#02338d]"></div>
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="inline-block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full mb-3">
                                {{ strtoupper($article->category ?? 'WELLNESS') }}
                            </span>
                            <h2 class="text-base font-extrabold text-slate-900 leading-snug">
                                {{ $article->title }}
                            </h2>
                            <p class="text-xs text-slate-500 italic mt-2 line-clamp-3">
                                {{ $article->summary ?? \Illuminate\Support\Str::limit(strip_tags((string) $article->body), 130) }}
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('content.show', $article) }}"
                               class="text-xs font-extrabold text-[#02338d] hover:underline">
                                Read Article →
                            </a>

                            <form method="POST" action="{{ route('content.bookmark', $article) }}">
                                @csrf
                                <button type="submit"
                                        title="{{ $isSaved ? 'Remove from Saved' : 'Save for Later' }}"
                                        class="p-2 rounded-xl transition {{ $isSaved ? 'text-[#02338d] bg-[#e8f0fe]' : 'text-slate-400 hover:text-slate-700 hover:bg-slate-100' }}">
                                    @if($isSaved)
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M5 3a2 2 0 00-2 2v16l9-4 9 4V5a2 2 0 00-2-2H5z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection