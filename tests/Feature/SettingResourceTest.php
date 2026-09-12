<?php

use App\Models\Language;
use App\Models\Setting;
use App\MoonShine\Resources\Setting\SettingResource;
use App\Services\Languages\LanguageService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Laravel\Models\MoonshineUser;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);
uses()->group('moonshine');

beforeEach(function (): void {
    app(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);

    $this->resource = app(SettingResource::class);
    $this->user = MoonshineUser::factory()->create();
});

it('index page', function (): void {
    actingAs($this->user, 'moonshine')
        ->get($this->resource->getIndexPageUrl())
        ->assertOk();
});

it('edit page', function (): void {
    $item = Setting::factory()->header()->ru()->create();

    actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl($item->getKey()))
        ->assertOk();
});

it('saves flexible-layouts blocks through the form', function (): void {
    $item = Setting::factory()->header()->ru()->create();

    actingAs($this->user, 'moonshine')
        ->patch(route('moonshine.crud.update', [
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $item->getKey(),
        ]), [
            'key' => 'header',
            'locale' => 'ru',
            'value' => [
                ['_type' => 'nav', 'links' => [['label' => 'Каталог', 'url' => '/catalog']]],
            ],
        ])
        ->assertRedirect();

    expect($item->refresh()->value)->toBe([
        ['_type' => 'nav', 'links' => [['label' => 'Каталог', 'url' => '/catalog']]],
    ]);
});

it('keeps key and locale unchanged on save', function (): void {
    $item = Setting::factory()->footer()->en()->create();

    actingAs($this->user, 'moonshine')
        ->patch(route('moonshine.crud.update', [
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $item->getKey(),
        ]), [
            'key' => 'header',
            'locale' => 'ru',
            'value' => null,
        ])
        ->assertRedirect();

    expect($item->refresh()->key)->toBe('footer')
        ->and($item->refresh()->locale)->toBe('en');
});

it('seeds header and footer for every active language', function (): void {
    $this->seed(SettingsSeeder::class);

    $rows = Setting::query()->get()
        ->map(fn (Setting $s): string => $s->key.'.'.$s->locale)
        ->all();

    expect($rows)->toEqualCanonicalizing(['header.ru', 'header.en', 'footer.ru', 'footer.en']);
});

it('seeder is re-runnable and does not duplicate rows', function (): void {
    $this->seed(SettingsSeeder::class);
    $this->seed(SettingsSeeder::class);

    expect(Setting::query()->count())->toBe(4);
});
