<?php

use App\Http\Middleware\SetLocale;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Setting;
use App\Services\Languages\LanguageService;
use App\Services\Settings\SettingService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();
});

it('returns language codes from the database ordered by sort_order', function (): void {
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);

    expect(resolve(LanguageService::class)->codes())->toBe(['ru', 'en']);
});

it('returns the default language code', function (): void {
    Language::factory()->default()->create(['code' => 'ru']);
    Language::factory()->create(['code' => 'en']);

    expect(resolve(LanguageService::class)->defaultCode())->toBe('ru');
});

it('falls back to config when the languages table is empty', function (): void {
    expect(resolve(LanguageService::class)->codes())->toBe(config('app.available_locales'))
        ->and(resolve(LanguageService::class)->defaultCode())->toBe(config('app.available_locales.0'));
});

it('flushes the cache when a language is saved', function (): void {
    Language::factory()->default()->create(['code' => 'ru']);
    Language::factory()->create(['code' => 'en']);

    $languageService = resolve(LanguageService::class);
    expect($languageService->codes())->toBe(['ru', 'en']);

    Language::factory()->create(['code' => 'de', 'sort_order' => 5]);

    expect($languageService->codes())->toBe(['ru', 'en', 'de']);
});

it('tracks a changed default language', function (): void {
    Language::factory()->default()->create(['code' => 'ru']);
    $languageService = resolve(LanguageService::class);
    expect($languageService->defaultCode())->toBe('ru');

    Language::query()->where('code', 'ru')->update(['is_default' => false]);
    Language::factory()->default()->create(['code' => 'en']);

    expect($languageService->defaultCode())->toBe('en');
});

it('accepts a dynamically added language in the locale middleware', function (): void {
    Language::factory()->default()->create(['code' => 'ru']);
    Language::factory()->create(['code' => 'de']);

    $middleware = new SetLocale;
    $response = $middleware->handle(Request::create('/de'), fn (): ResponseFactory|\Illuminate\Http\Response => response('ok'), 'de');

    expect(app()->getLocale())->toBe('de')
        ->and($response->getContent())->toBe('ok');
});

it('rejects an unknown locale in the middleware', function (): void {
    Language::factory()->default()->create(['code' => 'ru']);

    app()->setLocale('ru');
    $middleware = new SetLocale;
    $middleware->handle(Request::create('/xx'), fn (): ResponseFactory|\Illuminate\Http\Response => response('ok'), 'xx');

    expect(app()->getLocale())->toBe('ru');
});

it('falls back to the db default language in page translations', function (): void {
    Language::factory()->default()->create(['code' => 'ru']);
    Language::factory()->create(['code' => 'en']);

    $page = Page::factory()
        ->has(PageTranslation::factory()->ru(), 'translations')
        ->create();

    expect($page->translation('en')->locale)->toBe('ru');
});

it('falls back to the db default language in settings', function (): void {
    Language::factory()->default()->create(['code' => 'en']);
    Setting::factory()->header()->en()->withBlocks([['_type' => 'nav']])->create();

    expect(resolve(SettingService::class)->get('header', 'ru'))->toBe([['_type' => 'nav']]);
});

it('caches the language list between calls within the process', function (): void {
    Language::factory()->default()->create(['code' => 'ru']);

    $languageService = resolve(LanguageService::class);
    $languageService->codes();

    expect(Cache::has('languages.codes'))->toBeTrue();
});
