<?php

declare(strict_types=1);

namespace App\Services\Languages;

use App\Models\Language;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

/**
 * Single source of truth for active languages (admin-editable).
 *
 * Backed by Cache (rememberForever) with automatic invalidation through
 * Language model events. Falls back to config('app.available_locales')
 * when the table is empty or not migrated yet (e.g. during `migrate`).
 */
class LanguageService
{
    private const string CACHE_CODES = 'languages.codes';

    private const string CACHE_DEFAULT = 'languages.default';

    /**
     * Active language codes ordered by sort_order.
     *
     * Fully boot-safe: a missing/unmigrated database or an unavailable cache
     * store (e.g. sqlite file absent during `composer install` →
     * `package:discover` on CI) degrades to the config fallback instead of
     * breaking `artisan`/composer boot.
     *
     * @return list<string>
     */
    public function codes(): array
    {
        try {
            $codes = Cache::rememberForever(self::CACHE_CODES, function (): array {
                try {
                    $codes = Language::query()
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->pluck('code')
                        ->all();
                } catch (QueryException) {
                    return config('app.available_locales');
                }

                return $codes === [] ? config('app.available_locales') : $codes;
            });
        } catch (QueryException) {
            $codes = config('app.available_locales');
        }

        return $codes;
    }

    /**
     * Code of the default language (fallback locale, prefix-less routes).
     */
    public function defaultCode(): string
    {
        try {
            $default = Cache::rememberForever(self::CACHE_DEFAULT, function (): ?string {
                try {
                    return Language::query()->where('is_default', true)->value('code');
                } catch (QueryException) {
                    return null;
                }
            });
        } catch (QueryException) {
            $default = null;
        }

        return $default ?? config('app.available_locales.0');
    }

    /**
     * Flush the cached language list and default code.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_CODES);
        Cache::forget(self::CACHE_DEFAULT);
    }
}
