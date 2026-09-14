<?php

declare(strict_types=1);

namespace App\Services\Content;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Blog storefront use-cases: published articles listing (no server-side
 * pagination yet — the Alpine show-more handles the client batch) and
 * related-articles selection for the article page.
 */
class BlogService
{
    /**
     * All published articles, newest first, with eager-loaded translations.
     *
     * @return Collection<int, Article>
     */
    public function published(): Collection
    {
        $articles = Article::query()
            ->published()
            ->with('translations')
            ->orderByDesc('published_at')
            ->get();

        Log::debug('[BlogService.published] count={count}', ['count' => $articles->count()]);

        return $articles;
    }

    /**
     * Newest published articles except the given one — the article page
     * «Другие новости» section.
     *
     * @param  positive-int  $limit
     * @return Collection<int, Article>
     */
    public function related(Article $exclude, int $limit = 3): Collection
    {
        $related = Article::query()
            ->published()
            ->with('translations')
            ->whereKeyNot($exclude->getKey())
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        Log::debug('[BlogService.related] exclude_id={id} limit={limit} count={count}', [
            'id' => $exclude->getKey(),
            'limit' => $limit,
            'count' => $related->count(),
        ]);

        return $related;
    }
}
