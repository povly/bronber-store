<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\MediaFallback;
use App\Support\PageBlocks\PageBlockLibrary;
use App\Support\PageBreadcrumbs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    /**
     * Slug of the DB page that backs the site root («index» = home).
     */
    private const HOME_SLUG = 'index';

    /**
     * Render the site root from the DB page with slug «index» —
     * no static fallback: a missing page/translation is a 404, the
     * same contract as the catch-all route (the seeder ships content).
     */
    public function index(): Response
    {
        return $this->renderFixedSlug(self::HOME_SLUG);
    }

    /**
     * Render the only fixed page route (the site root) from its DB
     * page: a published page with a translation renders its blocks;
     * anything missing is a 404 — the same contract as the catch-all
     * route that serves every other page (the content is admin-managed,
     * there is no static-prototype fallback).
     */
    private function renderFixedSlug(string $slug): Response
    {
        $page = $this->findPublishedPage($slug);

        $translation = $page?->translation();

        if ($page === null || $translation === null) {
            Log::debug('[PageController] slug={slug} locale={locale} found=false', [
                'slug' => $slug,
                'locale' => app()->getLocale(),
            ]);

            abort(404);
        }

        Log::debug('[PageController] slug={slug} locale={locale} source=db', [
            'slug' => $slug,
            'locale' => $translation->locale,
        ]);

        return $this->renderPage($page, $translation);
    }

    /**
     * Render a published page by slug in the current locale
     * (fallback: default language → first translation; 404 otherwise).
     */
    public function show(string $slug): Response|RedirectResponse
    {
        if ($slug === self::HOME_SLUG) {
            return $this->redirectHome();
        }

        $page = $this->findPublishedPage($slug);

        if ($page === null) {
            Log::debug('[PageController.show] slug={slug} locale={locale} found=false', [
                'slug' => $slug,
                'locale' => app()->getLocale(),
            ]);

            abort(404);
        }

        $translation = $page->translation();

        abort_if($translation === null, 404);

        Log::debug('[PageController.show] slug={slug} locale={locale} found=true', [
            'slug' => $slug,
            'locale' => $translation->locale,
        ]);

        return $this->renderPage($page, $translation);
    }

    /**
     * «/index» and «/{locale}/index» are reserved for the site root —
     * permanently redirect them to the canonical home URL.
     */
    private function redirectHome(): RedirectResponse
    {
        $locale = app()->getLocale();
        $default = resolve(LanguageService::class)->defaultCode();

        $name = $locale === $default ? 'home' : "{$locale}.home";

        Log::debug('[PageController.show] slug=index redirected to={url}', [
            'url' => route($name),
        ]);

        return redirect()->route($name, [], 301);
    }

    /**
     * Find a published page with eager-loaded translations by slug.
     */
    private function findPublishedPage(string $slug): ?Page
    {
        return Page::query()
            ->published()
            ->with('translations')
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Render the flexible-layout blocks and share the SEO payload
     * with the layout.
     *
     * Block HTML is rendered by the page view itself (not here): block
     * views @push their assets onto layout stacks, which only works for
     * views rendered nested inside the page view render cycle.
     *
     * Empty media fields of a non-default translation inherit the
     * default language's value at render time (no DB duplication).
     */
    private function renderPage(Page $page, PageTranslation $translation): Response
    {
        view()->share('seo', $this->seo($page, $translation));

        // Page context for nested block views: the mediaImage editorjs
        // override renders converted <x-img> markup only on the about page.
        view()->share('currentPage', $page);

        $translation->content = MediaFallback::apply(
            $translation->content ?? [],
            $this->defaultContent($page, $translation),
            PageBlockLibrary::mediaSchemas(),
        );

        // Breadcrumbs are page-level: Главная → published ancestors → current.
        // The site root is canonical at /, a trail would duplicate it.
        $breadcrumbs = $page->slug === self::HOME_SLUG
            ? null
            : PageBreadcrumbs::forPage($page);

        return response()->view('page', [
            'translation' => $translation,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Content of the default-language translation — the media fallback
     * source; null when rendering the default translation itself.
     *
     * @return list<array<string, mixed>>|null
     */
    private function defaultContent(Page $page, PageTranslation $translation): ?array
    {
        $default = resolve(LanguageService::class)->defaultCode();

        if ($translation->locale === $default) {
            return null;
        }

        $content = $page->translations->firstWhere('locale', $default)?->content;

        return is_array($content) ? $content : null;
    }

    /**
     * Build the SEO payload for the layout partial, including hreflang
     * alternates for every locale the page is translated into. The home
     * page («index») is canonical at the site root, not at /index.
     *
     * @return array<string, mixed>
     */
    private function seo(Page $page, PageTranslation $pageTranslation): array
    {
        $default = resolve(LanguageService::class)->defaultCode();
        $isHome = $page->slug === self::HOME_SLUG;

        $alternates = [];

        foreach ($page->translations as $item) {
            if ($isHome) {
                $alternates[$item->locale] = $item->locale === $default
                    ? url('/')
                    : url("/{$item->locale}");

                continue;
            }

            $alternates[$item->locale] = $item->locale === $default
                ? url("/{$page->slug}")
                : url("/{$item->locale}/{$page->slug}");
        }

        if ($alternates !== []) {
            $alternates['x-default'] = $alternates[$default]
                ?? reset($alternates);
        }

        return [
            'title' => $pageTranslation->title,
            'meta_title' => $pageTranslation->meta_title,
            'meta_description' => $pageTranslation->meta_description,
            'meta_keywords' => $pageTranslation->meta_keywords,
            'meta_robots' => $pageTranslation->metaRobots(),
            'canonical_url' => $pageTranslation->canonical_url,
            'og_image' => $pageTranslation->og_image,
            'alternates' => $alternates,
        ];
    }
}
