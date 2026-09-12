<?php

use App\Http\Middleware\SetLocale;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Setting;
use App\Services\Languages\LanguageService;
use App\Services\Settings\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(LanguageService::class)->clearCache();
});

it('returns language codes from the database ordered by sort_order', function () {
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);

    expect(app(LanguageService::class)->codes())->toBe(['ru', 'en']);
});

it('returns the default language code', function () {
    Language::factory()->default()->create(['code' => 'ru']);
    Language::factory()->create(['code' => 'en']);

    expect(app(LanguageService::class)->defaultCode())->toBe('ru');
});

it('falls back to config when the languages table is empty', function () {
    expect(app(LanguageService::class)->codes())->toBe(config('app.available_locales'))
        ->and(app(LanguageService::class)->defaultCode())->toBe(config('app.available_locales.0'));
});

it('flushes the cache when a language is saved', function () {
    Language::factory()->default()->create(['code' => 'ru']);
    Language::factory()->create(['code' => 'en']);

    $service = app(LanguageService::class);
    expect($service->codes())->toBe(['ru', 'en']);

    Language::factory()->create(['code' => 'de', 'sort_order' => 5]);

    expect($service->codes())->toBe(['ru', 'en', 'de']);
});

it('tracks a changed default language', function () {
    Language::factory()->default()->create(['code' => 'ru']);
    $service = app(LanguageService::class);
    expect($service->defaultCode())->toBe('ru');

    Language::query()->where('code', 'ru')->update(['is_default' => false]);
    Language::factory()->default()->create(['code' => 'en']);

    expect($service->defaultCode())->toBe('en');
});

it('accepts a dynamically added language in the locale middleware', function () {
    Language::factory()->default()->create(['code' => 'ru']);
    Language::factory()->create(['code' => 'de']);

    $middleware = new SetLocale;
    $response = $middleware->handle(Request::create('/de'), fn () => response('ok'), 'de');

    expect(app()->getLocale())->toBe('de')
        ->and($response->getContent())->toBe('ok');
});

it('rejects an unknown locale in the middleware', function () {
    Language::factory()->default()->create(['code' => 'ru']);

    app()->setLocale('ru');
    $middleware = new SetLocale;
    $middleware->handle(Request::create('/xx'), fn () => response('ok'), 'xx');

    expect(app()->getLocale())->toBe('ru');
});

it('falls back to the db default language in page translations', function () {
    Language::factory()->default()->create(['code' => 'ru']);
    Language::factory()->create(['code' => 'en']);

    $page = Page::factory()
        ->has(PageTranslation::factory()->ru(), 'translations')
        ->create();

    expect($page->translation('en')->locale)->toBe('ru');
});

it('falls back to the db default language in settings', function () {
    Language::factory()->default()->create(['code' => 'en']);
    Setting::factory()->header()->en()->withBlocks([['_type' => 'nav']])->create();

    expect(app(SettingService::class)->get('header', 'ru'))->toBe([['_type' => 'nav']]);
});

it('caches the language list between calls within the process', function () {
    Language::factory()->default()->create(['code' => 'ru']);

    $service = app(LanguageService::class);
    $service->codes();

    expect(Cache::has('languages.codes'))->toBeTrue();
});
