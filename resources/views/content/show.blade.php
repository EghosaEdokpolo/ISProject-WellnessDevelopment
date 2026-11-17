@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-12">
    
    <!-- Breadcrumb -->
    <a href="{{ route('content.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-brand transition mb-8">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Content Hub
    </a>

    <!-- Article Card -->
    <article class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @if($content->image_url)
            <img src="{{ $content->image_url }}" alt="{{ $content->title }}" class="w-full h-72 object-cover">
        @else
            <div class="h-2 bg-brand"></div>
        @endif

        <div class="p-8 md:p-12">
            <div class="flex items-center gap-3 mb-6">
                @if($content->category)
                    <span class="text-[10px] font-bold uppercase tracking-wider text-brand bg-brand-light px-3 py-1.5 rounded-full">
                        {{ $content->category }}
                    </span>
                @endif
                <span class="text-xs font-medium text-slate-400">{{ $content->created_at->format('F d, Y') }}</span>
            </div>

            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                {{ $content->title }}
            </h1>

            <div class="prose prose-slate prose-lg max-w-none text-slate-600 leading-relaxed mb-10">
                {!! nl2br(e($content->body)) !!}
            </div>

            <div class="border-t border-slate-100 pt-8 flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">Found this helpful?</p>
                
                <form method="POST" action="{{ route('content.save', $content->slug) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 font-bold px-6 py-3 rounded-xl transition-all shadow-sm {{ $isSaved ? 'bg-brand-light text-brand border border-brand/20' : 'bg-brand text-white hover:bg-blue-800' }}">
                        <svg class="w-5 h-5" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        {{ $isSaved ? 'Saved to your list' : 'Save for later' }}
                    </button>
                </form>
            </div>
        </div>
    </article>
</div>
@endsection