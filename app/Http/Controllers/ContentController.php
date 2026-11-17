<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(Request $request): View
    {
        $activeCategory = $request->query('category');
        $search         = $request->query('q');

        $contents = Content::query()
            ->where('is_published', true)
            ->when($activeCategory, fn ($q, $c) => $q->where('category', $c))
            ->when($search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()
            ->paginate(12);

        $savedIds = auth()->user()
            ->savedContents()
            ->pluck('contents.id')
            ->all();

        return view('content.index', compact('contents', 'activeCategory', 'search', 'savedIds'));
    }

    public function show(string $slug): View
    {
        $content = Content::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $isSaved = auth()->user()
            ->savedContents()
            ->where('contents.id', $content->id)
            ->exists();

        return view('content.show', compact('content', 'isSaved'));
    }

    public function toggleSave(string $slug): RedirectResponse
    {
        $content = Content::where('slug', $slug)->firstOrFail();
        $user    = auth()->user();

        $existing = $user->savedContents()
            ->where('contents.id', $content->id)
            ->exists();

        if ($existing) {
            $user->savedContents()->detach($content->id);
            return back()->with('success', 'Removed from saved items.');
        }

        $user->savedContents()->attach($content->id);
        return back()->with('success', 'Saved for later.');
    }
}