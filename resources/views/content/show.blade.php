@extends('layouts.app')

@section('content')
    <!-- Reading Progress Bar -->
    <div class="fixed top-0 left-0 h-1.5 bg-wl-gold z-50 transition-all duration-75 ease-out" id="progress-bar" style="width: 0%;"></div>

    <div class="max-w-3xl mx-auto">
        
        <!-- Top Navigation & Actions -->
        <div class="flex justify-between items-center mb-8">
            <a href="{{ route('content.index') }}" class="text-sm font-bold text-gray-500 hover:text-wl-indigo transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Hub
            </a>

            <div class="flex items-center gap-3">
                <!-- Share Button -->
                <button onclick="copyToClipboard()" class="flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-wl-indigo transition-colors bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    Share
                </button>

                <!-- Bookmark Toggle -->
                <form method="POST" action="{{ route('content.save', $slug) }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-xs font-bold {{ $isSaved ? 'text-wl-gold bg-wl-gold/10 border-wl-gold/30' : 'text-gray-500 bg-white border-gray-200 hover:text-wl-gold' }} px-3 py-1.5 rounded-lg border shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                        {{ $isSaved ? 'Saved' : 'Save' }}
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Article Header -->
        <div class="text-center mb-10">
            <div class="text-6xl mb-6">{{ $article['emoji'] }}</div>
            <div class="flex items-center justify-center gap-3 mb-4">
                <span class="inline-block {{ $article['color'] }} text-[10px] font-bold px-3 py-1 rounded-md uppercase tracking-wider">{{ $article['category'] }}</span>
                <span class="text-xs font-bold text-gray-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $readTime }} min read
                </span>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight">{{ $article['title'] }}</h1>
        </div>

        <!-- Content Body -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12 mb-12">
            <div class="prose prose-lg prose-indigo text-gray-700 leading-relaxed">
                {!! $article['content'] !!}
            </div>
        </div>

        <!-- Related Articles -->
        @if(count($related) > 0)
            <div class="mb-12">
                <h3 class="text-xl font-bold text-gray-900 mb-4 border-b border-gray-200 pb-2">Keep Reading</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($related as $relSlug => $relArticle)
                        <a href="{{ route('content.show', $relSlug) }}" class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow flex items-start gap-4 group">
                            <div class="text-3xl bg-gray-50 p-2 rounded-lg">{{ $relArticle['emoji'] }}</div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $relArticle['category'] }}</span>
                                <h4 class="font-bold text-gray-900 text-sm mt-1 group-hover:text-wl-indigo transition-colors">{{ $relArticle['title'] }}</h4>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- CTA -->
        <div class="bg-wl-lavender/40 rounded-2xl p-8 border border-wl-indigo/10 flex flex-col md:flex-row items-center justify-between gap-4 text-center md:text-left">
            <div>
                <h4 class="font-bold text-wl-indigo text-lg">Ready to practice what you read?</h4>
                <p class="text-sm text-gray-600 mt-1">Register for an upcoming wellness event and earn points.</p>
            </div>
            <a href="{{ route('events.index') }}" class="bg-wl-indigo text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-indigo-700 transition shadow-sm whitespace-nowrap">
                View Events
            </a>
        </div>

    </div>

    <script>
        // Reading Progress Bar Logic
        window.onscroll = function() {
            var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            var scrolled = (winScroll / height) * 100;
            document.getElementById("progress-bar").style.width = scrolled + "%";
        };

        // Copy to Clipboard Logic
        function copyToClipboard() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link copied to clipboard!');
            });
        }
    </script>
@endsection