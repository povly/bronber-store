<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Page;
use App\Support\PageBlocks\LinkResolver;
use Illuminate\Support\Facades\Log;

/**
 * Automatic breadcrumbs for DB pages: Главная → …published ancestors →
 * current page. Rendered by the page view, not by content blocks.
 *
 * Ancestor hrefs reuse {@see LinkResolver} (published pages only,
 * localized for the render locale); the current item carries no url —
 * the breadcrumbs component marks it as the current page.
 */
final class PageBreadcrumbs
{
    /**
     * Hard guard against parent cycles (bad data) — the trail never
     * walks deeper than this many ancestors.
     */
    private const MAX_DEPTH = 10;

    /**
     * Build the breadcrumb trail for a page in the given locale.
     *
     * @param  Page  $page  the page being rendered (current item)
     * @return list<array{label: string, url: string|null}>
     */
    public static function forPage(Page $page, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        $items = [
            ['label' => __('store.breadcrumbs_home'), 'url' => route('home')],
        ];

        foreach (self::ancestors($page) as $ancestor) {
            $translation = $ancestor->translation($locale);

            if ($translation === null) {
                continue;
            }

            $href = LinkResolver::href(['type' => 'page', 'page' => $ancestor->slug], $locale);

            if ($href === null) {
                continue;
            }

            $items[] = ['label' => $translation->title, 'url' => $href];
        }

        $current = $page->translation($locale)?->title;

        if ($current !== null) {
            $items[] = ['label' => $current, 'url' => null];
        }

        Log::debug('[PageBreadcrumbs] slug={slug} locale={locale} items={items}', [
            'slug' => $page->slug,
            'locale' => $locale,
            'items' => count($items),
        ]);

        return $items;
    }

    /**
     * Ancestors root-first (nearest parent first reversed), cycle- and
     * depth-guarded. Unpublished ancestors stay in the trail — the
     * resolver skips them by href, but the walk keeps the chain intact.
     *
     * @return list<Page>
     */
    private static function ancestors(Page $page): array
    {
        $chain = [];
        $seen = [(string) $page->getKey()];
        $current = $page;

        for ($depth = 0; $depth < self::MAX_DEPTH; $depth++) {
            $parent = $current->parent()->first();

            if ($parent === null || in_array((string) $parent->getKey(), $seen, true)) {
                break;
            }

            $seen[] = (string) $parent->getKey();
            $chain[] = $parent;
            $current = $parent;
        }

        if ($current->parent()->exists()) {
            Log::warning('[PageBreadcrumbs] slug={slug} ancestor chain cut at depth={depth} (cycle or too deep)', [
                'slug' => $page->slug,
                'depth' => self::MAX_DEPTH,
            ]);
        }

        return $chain;
    }
}
