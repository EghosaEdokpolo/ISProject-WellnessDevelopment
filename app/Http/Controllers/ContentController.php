<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ContentController extends Controller
{
    /**
     * Display the Strathmore University Wellness Content Hub.
     */
    public function index(Request $request): View
    {
        $user     = $request->user();
        $category = $request->query('category');
        $type     = $request->query('type');
        $search   = trim((string) $request->query('search', ''));
        $saved    = $request->boolean('saved') || $category === 'saved';

        $bookmarkedKeys = $this->getUserBookmarkedIdentifiers((int) $user->id);

        if (Schema::hasTable('contents')) {
            $query = Content::query();

            if ($category && ! in_array($category, ['all', 'saved'], true)) {
                $query->where('category', $category);
            }

            if ($type && $type !== 'all') {
                $query->where('type', $type);
            }

            if ($search !== '') {
                $query->where(function ($q) use ($search): void {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('summary', 'like', "%{$search}%")
                      ->orWhere('body', 'like', "%{$search}%");
                });
            }

            $articles = $query->latest()->get()->map(function ($item) use ($bookmarkedKeys) {
                $isSaved = in_array((string) $item->slug, $bookmarkedKeys, true)
                    || in_array((string) $item->id, $bookmarkedKeys, true);

                $item->is_saved      = $isSaved;
                $item->is_bookmarked = $isSaved;

                return $item;
            });

            if ($saved) {
                $articles = $articles->filter(fn ($item) => $item->is_saved)->values();
            }
        } else {
            $articles = collect();
        }

        return view('content.index', [
            'articles'         => $articles,
            'contents'         => $articles,
            'bookmarkedIds'    => $bookmarkedKeys,
            'bookmarkedSlugs'  => $bookmarkedKeys,
            'savedIds'         => $bookmarkedKeys,
            'savedSlugs'       => $bookmarkedKeys,
            'activeCategory'   => $category ?: 'all',
            'selectedCategory' => $category ?: 'all',
            'activeType'       => $type ?: 'all',
            'search'           => $search,
            'onlySaved'        => $saved,
        ]);
    }

    /**
     * Display a single wellness article, guide, or video resource.
     */
    public function show(Request $request, string $slug): View
    {
        $user           = $request->user();
        $bookmarkedKeys = $this->getUserBookmarkedIdentifiers((int) $user->id);

        $article = Schema::hasTable('contents')
            ? Content::where('slug', $slug)->orWhere('id', $slug)->firstOrFail()
            : abort(404);

        $isSaved = in_array((string) $article->slug, $bookmarkedKeys, true)
            || in_array((string) $article->id, $bookmarkedKeys, true);

        $article->is_saved      = $isSaved;
        $article->is_bookmarked = $isSaved;

        $related = Content::where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->limit(3)
            ->get();

        return view('content.show', [
            'article'         => $article,
            'content'         => $article,
            'isSaved'         => $isSaved,
            'isBookmarked'    => $isSaved,
            'bookmarkedIds'   => $bookmarkedKeys,
            'bookmarkedSlugs' => $bookmarkedKeys,
            'related'         => $related,
        ]);
    }

    /**
     * Toggle saving/bookmarking a content item for the authenticated staff member.
     */
    public function toggleSave(Request $request, string $slug): RedirectResponse
    {
        $userId  = (int) $request->user()->id;
        $content = Schema::hasTable('contents')
            ? Content::where('slug', $slug)->orWhere('id', $slug)->first()
            : null;

        [$targetTable, $targetCol] = $this->resolveBookmarkTarget();

        if (! $targetTable || ! $targetCol) {
            return back()->with('success', 'Content preference updated.');
        }

        $value = ($targetCol === 'content_id')
            ? ($content ? $content->id : (int) $slug)
            : ($content ? $content->slug : $slug);

        $exists = DB::table($targetTable)
            ->where('user_id', $userId)
            ->where($targetCol, $value)
            ->exists();

        if ($exists) {
            DB::table($targetTable)
                ->where('user_id', $userId)
                ->where($targetCol, $value)
                ->delete();

            return back()->with('success', 'Removed from your saved wellness resources.');
        }

        $insertData = [
            'user_id'  => $userId,
            $targetCol => $value,
        ];

        if (Schema::hasColumn($targetTable, 'created_at')) {
            $insertData['created_at'] = now();
        }
        if (Schema::hasColumn($targetTable, 'updated_at')) {
            $insertData['updated_at'] = now();
        }

        DB::table($targetTable)->insert($insertData);

        return back()->with('success', 'Saved to your personal wellness library.');
    }

    /**
     * @return array<int, string>
     */
    private function getUserBookmarkedIdentifiers(int $userId): array
    {
        [$table, $column] = $this->resolveBookmarkTarget();
        if (! $table || ! $column) {
            return [];
        }

        return DB::table($table)
            ->where('user_id', $userId)
            ->pluck($column)
            ->map(fn ($val): string => (string) $val)
            ->all();
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function resolveBookmarkTarget(): array
    {
        if (Schema::hasTable('content_bookmarks')) {
            $cols = Schema::getColumnListing('content_bookmarks');
            foreach (['article_slug', 'content_slug', 'slug', 'content_id', 'resource_id'] as $candidate) {
                if (in_array($candidate, $cols, true)) {
                    return ['content_bookmarks', $candidate];
                }
            }
        }

        if (Schema::hasTable('content_user')) {
            $cols = Schema::getColumnListing('content_user');
            foreach (['content_id', 'article_slug', 'slug'] as $candidate) {
                if (in_array($candidate, $cols, true)) {
                    return ['content_user', $candidate];
                }
            }
        }

        return [null, null];
    }
}