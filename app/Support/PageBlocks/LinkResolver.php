<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Services\Languages\LanguageService;
use Illuminate\Support\Facades\Log;

/**
 * Resolves a two-type link ({label, type: page|custom, page, url}) to an href.
 *
 * - page: slug of a published page, localized for the render locale
 *   (default locale → /{slug}, others → /{locale}/{slug});
 * - custom: the URL as-is (relative paths are authored per locale by the admin).
 *
 * Broken links resolve to null so the caller can skip the item —
 * a menu must never render an empty <a href="">.
 */
class LinkResolver
{
    /**
     * @param  array<string, mixed>  $link
     */
    public static function href(array $link, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $type = $link['type'] ?? null;

        if ($type === 'page') {
            $slug = trim((string) ($link['page'] ?? ''));

            if ($slug === '') {
                Log::warning('[LinkResolver] page link without slug skipped');

                return null;
            }

            if (! PageOptions::isPublished($slug)) {
                Log::warning('[LinkResolver] page not found or unpublished, slug={slug}', ['slug' => $slug]);

                return null;
            }

            $default = app(LanguageService::class)->defaultCode();

            return $locale === $default
                ? url("/{$slug}")
                : url("/{$locale}/{$slug}");
        }

        if ($type === 'custom') {
            $url = trim((string) ($link['url'] ?? ''));

            if ($url === '') {
                Log::warning('[LinkResolver] custom link without url skipped');

                return null;
            }

            return $url;
        }

        Log::warning('[LinkResolver] invalid link type={type} skipped', ['type' => (string) $type]);

        return null;
    }
}
