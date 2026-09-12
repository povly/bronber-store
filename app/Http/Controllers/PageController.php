<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\BlockRenderer;
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
     * Render the site root from the DB page with slug «index»
     * (fallback: the static prototype view when the page or its
     * translation is missing — same pattern as header/footer settings).
     */
    public function index(): Response
    {
        $page = $this->findPublishedPage(self::HOME_SLUG);
        $translation = $page?->translation();

        if ($translation === null) {
            Log::debug('[PageController.index] locale={locale} source=fallback', [
                'locale' => app()->getLocale(),
            ]);

            return response()->view('home');
        }

        Log::debug('[PageController.index] locale={locale} source=db', [
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
     */
    private function renderPage(Page $page, PageTranslation $translation): Response
    {
        $html = resolve(BlockRenderer::class)->render($translation->content ?? [], 'page');

        view()->share('seo', $this->seo($page, $translation));

        return response()->view('page', [
            'html' => $html,
            'translation' => $translation,
        ]);
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
