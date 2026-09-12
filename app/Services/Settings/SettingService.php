<?php

declare(strict_types=1);

namespace App\Services\Settings;

use App\Models\Setting;
use App\Services\Languages\LanguageService;
use Illuminate\Support\Facades\Log;

/**
 * Reads settings (key × locale) with a per-request runtime cache.
 *
 * Locale fallback: requested locale → default language → empty array
 * (callers fall back to their static defaults when nothing is returned).
 */
class SettingService
{
    /**
     * Per-request cache: "{key}.{locale}" => resolved value.
     *
     * @var array<string, array<array-key, mixed>>
     */
    private array $cache = [];

    /**
     * Get a setting value by key and locale with ru fallback.
     *
     * @return array<array-key, mixed>
     */
    public function get(string $key, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        $cacheKey = $key.'.'.$locale;

        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }

        $value = $this->resolve($key, $locale);
        $this->cache[$cacheKey] = $value;

        Log::debug('[SettingService.get] key={key} locale={locale} resolved={bool}', [
            'key' => $key,
            'locale' => $locale,
            'resolved' => $value !== [],
        ]);

        return $value;
    }

    /**
     * Resolve a setting from the database: requested locale first, default language as fallback.
     *
     * @return array<array-key, mixed>
     */
    private function resolve(string $key, string $locale): array
    {
        $default = app(LanguageService::class)->defaultCode();

        $settings = Setting::query()
            ->where('key', $key)
            ->whereIn('locale', [$locale, $default])
            ->get()
            ->keyBy('locale');

        return ($settings->get($locale) ?? $settings->get($default))?->value ?? [];
    }
}
