<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Services\Content\BlogService;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\ArticleBlockLibrary;
use App\Support\PageBlocks\MediaFallback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Blog storefront: listing (/blog) and article page (/blog/{slug}).
 *
 * The blog is an entity module with a listing — a justified deviation
 * from the "pages only via catch-all" rule (.ai/rules/routes.md):
 * like the future catalog, it has fixed routes above the catch-all.
 */
class BlogController extends Controller
{
    /**
     * Blog listing: the first page (3 cards) renders server-side; the
     * «Показать больше» button fetches the next pages over AJAX
     * ({@see self::cards()}).
     */
    public function index(): Response
    {
        $articles = resolve(BlogService::class)->page();

        Log::debug('[BlogController.index] locale={locale} page={page} total={total}', [
            'locale' => app()->getLocale(),
            'page' => $articles->currentPage(),
            'total' => $articles->total(),
        ]);

        view()->share('seo', $this->listingSeo());

        return response()->view('blog', ['articles' => $articles]);
    }

    /**
     * AJAX fragment for the listing «Показать больше»: the next page of
     * article cards as a {html, has_more} JSON payload. Whitelist-only
     * response — no ORM models are serialized.
     */
    public function cards(): JsonResponse
    {
        $page = max(1, (int) request()->query('page', '1'));
        $articles = resolve(BlogService::class)->page($page);

        Log::debug('[BlogController.cards] locale={locale} page={page} count={count} has_more={has_more}', [
            'locale' => app()->getLocale(),
            'page' => $page,
            'count' => $articles->count(),
            'has_more' => $articles->hasMorePages(),
        ]);

        return response()->json([
            'html' => view('blocks.blog.cards', ['articles' => $articles->getCollection()])->render(),
            'has_more' => $articles->hasMorePages(),
        ]);
    }

    /**
     * Render a published article by slug in the current locale
     * (fallback: default language → first translation; 404 otherwise).
     */
    public function show(string $slug): Response
    {
        $article = Article::query()
            ->published()
            ->with('translations')
            ->where('slug', $slug)
            ->first();

        $translation = $article?->translation();

        if ($article === null || $translation === null) {
            Log::debug('[BlogController.show] slug={slug} locale={locale} found=false', [
                'slug' => $slug,
                'locale' => app()->getLocale(),
            ]);

            abort(404);
        }

        Log::debug('[BlogController.show] slug={slug} locale={locale} found=true', [
            'slug' => $slug,
            'locale' => $translation->locale,
        ]);

        view()->share('seo', $this->seo($article, $translation));

        // Article context for nested block views: the «Другие новости»
        // block (article-related) reads the shared list — BlockRenderer
        // renders block views with only $block in scope.
        view()->share('related', resolve(BlogService::class)->related($article));

        $translation->content = MediaFallback::apply(
            $translation->content ?? [],
            $this->defaultContent($article, $translation),
            ArticleBlockLibrary::mediaSchemas(),
        );

        return response()->view('article', [
            'article' => $article,
            'translation' => $translation,
            'breadcrumbs' => $this->breadcrumbs($translation),
        ]);
    }

    /**
     * SEO payload for the listing (lang keys, hreflang over every
     * registered locale: /blog ↔ /en/blog).
     *
     * @return array<string, mixed>
     */
    private function listingSeo(): array
    {
        $default = resolve(LanguageService::class)->defaultCode();

        $alternates = [];

        foreach (resolve(LanguageService::class)->codes() as $code) {
            $alternates[$code] = $code === $default
                ? url('/blog')
                : url("/{$code}/blog");
        }

        if ($alternates !== []) {
            $alternates['x-default'] = $alternates[$default]
                ?? reset($alternates);
        }

        return [
            'title' => __('store.blog_title'),
            'meta_title' => __('store.blog_meta_title'),
            'meta_description' => __('store.blog_meta_description'),
            'meta_keywords' => null,
            'meta_robots' => 'index, follow',
            'canonical_url' => null,
            'og_image' => null,
            'alternates' => $alternates,
        ];
    }

    /**
     * Build the SEO payload for the article page, including hreflang
     * alternates for every locale the article is translated into.
     *
     * @return array<string, mixed>
     */
    private function seo(Article $article, ArticleTranslation $translation): array
    {
        $default = resolve(LanguageService::class)->defaultCode();

        $alternates = [];

        foreach ($article->translations as $item) {
            $alternates[$item->locale] = $item->locale === $default
                ? url("/blog/{$article->slug}")
                : url("/{$item->locale}/blog/{$article->slug}");
        }

        if ($alternates !== []) {
            $alternates['x-default'] = $alternates[$default]
                ?? reset($alternates);
        }

        return [
            'title' => $translation->title,
            'meta_title' => $translation->meta_title,
            'meta_description' => $translation->meta_description,
            'meta_keywords' => $translation->meta_keywords,
            'meta_robots' => $translation->metaRobots(),
            'canonical_url' => $translation->canonical_url,
            'og_image' => $translation->og_image,
            'alternates' => $alternates,
        ];
    }

    /**
     * Breadcrumbs: Главная → Блог (localized) → article title.
     *
     * @return list<array{label: string, url: string|null}>
     */
    private function breadcrumbs(ArticleTranslation $translation): array
    {
        $locale = app()->getLocale();
        $default = resolve(LanguageService::class)->defaultCode();

        $homeUrl = $locale === $default
            ? route('home')
            : route("{$locale}.home");

        $blogUrl = $locale === $default
            ? url('/blog')
            : url("/{$locale}/blog");

        return [
            ['label' => __('store.breadcrumbs_home'), 'url' => $homeUrl],
            ['label' => __('store.blog_title'), 'url' => $blogUrl],
            ['label' => $translation->title, 'url' => null],
        ];
    }

    /**
     * Content of the default-language translation — the media fallback
     * source; null when rendering the default translation itself.
     *
     * @return list<array<string, mixed>>|null
     */
    private function defaultContent(Article $article, ArticleTranslation $translation): ?array
    {
        $default = resolve(LanguageService::class)->defaultCode();

        if ($translation->locale === $default) {
            return null;
        }

        $content = $article->translations->firstWhere('locale', $default)?->content;

        return is_array($content) ? $content : null;
    }
}
