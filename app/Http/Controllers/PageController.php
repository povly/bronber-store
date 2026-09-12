<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\BlockRenderer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    /**
     * Render a published page by slug in the current locale
     * (fallback: default language → first translation; 404 otherwise).
     */
    public function show(string $slug): Response
    {
        $page = Page::query()
            ->published()
            ->with('translations')
            ->where('slug', $slug)
            ->first();

        if ($page === null) {
            Log::debug('[PageController.show] slug={slug} locale={locale} found=false', [
                'slug' => $slug,
                'locale' => app()->getLocale(),
            ]);

            abort(404);
        }

        $translation = $page->translation();

        abort_if($translation === null, 404);

        $html = resolve(BlockRenderer::class)->render($translation->content ?? [], 'page');

        view()->share('seo', $this->seo($page, $translation));

        Log::debug('[PageController.show] slug={slug} locale={locale} found=true', [
            'slug' => $slug,
            'locale' => $translation->locale,
        ]);

        return response()->view('page', [
            'html' => $html,
            'translation' => $translation,
        ]);
    }

    /**
     * Build the SEO payload for the layout partial, including hreflang
     * alternates for every locale the page is translated into.
     *
     * @return array<string, mixed>
     */
    private function seo(Page $page, PageTranslation $pageTranslation): array
    {
        $default = resolve(LanguageService::class)->defaultCode();

        $alternates = [];

        foreach ($page->translations as $item) {
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
