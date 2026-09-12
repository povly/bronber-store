<?php

declare(strict_types=1);

namespace App\Support\PageBlocks;

use App\Models\Page;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

/**
 * Published-page options (slug → title in the current/fallback locale)
 * for "page" link selects in MoonShine block forms.
 *
 * Cached statically per request — a menu form asks for the list once per field.
 * Boot-safe: a missing/unmigrated pages table degrades to an empty list.
 */
class PageOptions
{
    /** @var array<string, string>|null */
    private static ?array $cache = null;

    /**
     * @return array<string, string> slug => localized title
     */
    public static function published(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        try {
            $pages = Page::query()
                ->published()
                ->with('translations')
                ->get();
        } catch (QueryException) {
            return self::$cache = [];
        }

        $options = [];

        foreach ($pages as $page) {
            $translation = $page->translation();

            if ($translation !== null) {
                $options[$page->slug] = $translation->title;
            }
        }

        Log::debug('[PageOptions] loaded', ['count' => count($options)]);

        return self::$cache = $options;
    }

    /**
     * Whether a page with this slug is published.
     */
    public static function isPublished(string $slug): bool
    {
        return array_key_exists($slug, self::published());
    }

    /**
     * Flush the per-request cache (used by tests).
     */
    public static function flush(): void
    {
        self::$cache = null;
    }
}
