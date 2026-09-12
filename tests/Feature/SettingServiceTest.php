<?php

use App\Models\Setting;
use App\Services\Languages\LanguageService;
use App\Services\Settings\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();
});

it('returns the setting for the requested locale', function (): void {
    Setting::factory()->header()->ru()->withBlocks([['_type' => 'nav']])->create();
    Setting::factory()->header()->en()->withBlocks([['_type' => 'nav', 'label' => 'Shop']])->create();

    $settingService = resolve(SettingService::class);

    expect($settingService->get('header', 'en'))->toBe([['_type' => 'nav', 'label' => 'Shop']])
        ->and($settingService->get('header', 'ru'))->toBe([['_type' => 'nav']]);
});

it('falls back to ru when the locale is missing', function (): void {
    Setting::factory()->header()->ru()->withBlocks([['_type' => 'nav']])->create();

    expect(resolve(SettingService::class)->get('header', 'en'))->toBe([['_type' => 'nav']]);
});

it('returns an empty array when nothing is found', function (): void {
    expect(resolve(SettingService::class)->get('header', 'en'))->toBe([]);
});

it('uses the current application locale when none is passed', function (): void {
    Setting::factory()->header()->en()->withBlocks([['_type' => 'nav']])->create();

    app()->setLocale('en');

    expect(resolve(SettingService::class)->get('header'))->toBe([['_type' => 'nav']]);
});

/**
 * Count queries that read the settings table, quote-style agnostic
 * (SQLite uses double quotes, MySQL/MariaDB uses backticks).
 */
function settingsTableQueries(array $queryLog): int
{
    return collect($queryLog)
        ->filter(fn (array $q): bool => str_contains($q['query'], 'from "settings"')
            || str_contains($q['query'], 'from `settings`'))
        ->count();
}

it('caches resolved values within a single request', function (): void {
    Setting::factory()->header()->ru()->withBlocks([['_type' => 'nav']])->create();

    $settingService = resolve(SettingService::class);

    DB::enableQueryLog();
    $settingService->get('header', 'ru');
    $settingService->get('header', 'ru');

    $settingsQueries = settingsTableQueries(DB::getQueryLog());
    DB::disableQueryLog();

    expect($settingsQueries)->toBe(1);
});

it('queries the database again for a different locale or key', function (): void {
    Setting::factory()->header()->ru()->withBlocks([['_type' => 'nav']])->create();
    Setting::factory()->footer()->ru()->withBlocks([['_type' => 'copyright']])->create();

    $settingService = resolve(SettingService::class);

    DB::enableQueryLog();
    $settingService->get('header', 'ru');
    $settingService->get('footer', 'ru');
    $settingService->get('header', 'en');

    $settingsQueries = settingsTableQueries(DB::getQueryLog());
    DB::disableQueryLog();

    expect($settingsQueries)->toBe(3);
});

it('exposes the static model accessor with the same behaviour', function (): void {
    Setting::factory()->header()->ru()->withBlocks([['_type' => 'nav']])->create();

    expect(Setting::get('header', 'ru'))->toBe([['_type' => 'nav']])
        ->and(Setting::get('header', 'en'))->toBe([['_type' => 'nav']])
        ->and(Setting::get('missing', 'ru'))->toBe([]);
});

it('logs key, locale and resolved flag on debug level', function (): void {
    Log::spy();

    resolve(SettingService::class)->get('header', 'ru');

    Log::shouldHaveReceived('debug')->once()->with(
        '[SettingService.get] key={key} locale={locale} resolved={bool}',
        ['key' => 'header', 'locale' => 'ru', 'resolved' => false],
    );
});
