<?php

declare(strict_types=1);

namespace App\Services\Content;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Blog storefront use-cases: the listing is paginated server-side (the
 * first page renders in the blog view, «Показать больше» fetches the next
 * pages over AJAX) plus related-articles selection for the article page.
 */
class BlogService
{
    /**
     * One page of published articles, newest first, with eager-loaded
     * translations.
     *
     * @param  positive-int  $page
     * @param  positive-int  $perPage  how many cards one page carries
     * @return LengthAwarePaginator<Article>
     */
    public function page(int $page = 1, int $perPage = 3): LengthAwarePaginator
    {
        $articles = Article::query()
            ->published()
            ->with('translations')
            ->orderByDesc('published_at')
            ->paginate($perPage, ['*'], 'page', $page);

        Log::debug('[BlogService.page] page={page} per_page={per_page} total={total}', [
            'page' => $articles->currentPage(),
            'per_page' => $perPage,
            'total' => $articles->total(),
        ]);

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
