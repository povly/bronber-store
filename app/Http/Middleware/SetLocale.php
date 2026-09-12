<?php

namespace App\Http\Middleware;

use App\Services\Languages\LanguageService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $locale = null): Response
    {
        $languages = app(LanguageService::class);
        $locale ??= $languages->defaultCode();

        if (in_array($locale, $languages->codes(), true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
