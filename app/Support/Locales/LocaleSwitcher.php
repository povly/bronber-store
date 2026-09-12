<?php

declare(strict_types=1);

namespace App\Support\Locales;

use App\Services\Languages\LanguageService;
use Illuminate\Support\Facades\Log;

/**
 * Builds language-switcher URLs that keep the current page path.
 *
 * The default locale keeps the prefix-less form ("/"), every other
 * active locale gets its own prefix ("/en", "/en/contacts"). The
 * current non-default locale prefix is stripped from the request path
 * before the target prefix is applied, so switching languages stays
 * on the same page. The query string is intentionally dropped
 * (prototype-stage decision).
 */
class LocaleSwitcher
{
    /**
     * URL of the current page in the target locale.
     *
     * @param  string  $target  active locale code (LanguageService::codes())
     */
    public static function href(string $target): string
    {
        $languageService = resolve(LanguageService::class);
        $default = $languageService->defaultCode();

        $path = trim(request()->path(), '/');

        // Strip the current non-default locale prefix ("/en/…" → "…").
        foreach (array_diff($languageService->codes(), [$default]) as $code) {
            if ($path === $code) {
                $path = '';

                break;
            }

            if (str_starts_with($path, $code.'/')) {
                $path = mb_substr($path, mb_strlen($code) + 1);

                break;
            }
        }

        $href = $target === $default
            ? ($path === '' ? '/' : '/'.$path)
            : '/'.$target.($path === '' ? '' : '/'.$path);

        Log::debug('[LocaleSwitcher] from={from} to={to} path={path}', [
            'from' => app()->getLocale(),
            'to' => $target,
            'path' => $path,
        ]);

        return url($href);
    }
}
