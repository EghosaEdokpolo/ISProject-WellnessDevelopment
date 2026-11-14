<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContentView;
use App\Models\SavedArticle;
use App\Services\ContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(
        private readonly ContentService $contentService
    ) {}

    public function index(Request $request): View
    {
        $articles = $this->contentService->getAllArticles();

        // Filter by Category
        if ($request->filled('category') && $request->category !== 'All') {
            $articles = array_filter($articles, fn($a) => $a['category'] === $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $articles = array_filter($articles, function($a) use ($search) {
                return str_contains(strtolower($a['title']), $search) || str_contains(strtolower($a['desc']), $search);
            });
        }

        $savedSlugs = SavedArticle::where('user_id', auth()->id())->pluck('article_slug')->toArray();

        return view('content.index', compact('articles', 'savedSlugs'));
    }

    public function show(string $slug): View
    {
        $articles = $this->contentService->getAllArticles();
        
        if (!isset($articles[$slug])) {
            abort(404);
        }

        $article = $articles[$slug];
        $readTime = $this->contentService->calculateReadTime($article['content']);

        // Log the read event
        ContentView::firstOrCreate(['user_id' => auth()->id(), 'article_slug' => $slug]);

        $isSaved = SavedArticle::where('user_id', auth()->id())->where('article_slug', $slug)->exists();
        
        // Find related articles in the same category
        $related = array_filter($articles, fn($a, $key) => $a['category'] === $article['category'] && $key !== $slug, ARRAY_FILTER_USE_BOTH);
        $related = array_slice($related, 0, 2);

        return view('content.show', compact('article', 'readTime', 'isSaved', 'slug', 'related'));
    }

    public function toggleSave(string $slug): RedirectResponse
    {
        $saved = SavedArticle::where('user_id', auth()->id())->where('article_slug', $slug)->first();
        
        if ($saved) {
            $saved->delete();
            $message = 'Removed from bookmarks.';
        } else {
            SavedArticle::create(['user_id' => auth()->id(), 'article_slug' => $slug]);
            $message = 'Saved to bookmarks!';
        }

        return back()->with('status', $message);
    }
}