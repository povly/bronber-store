<?php

use App\Models\Language;
use App\MoonShine\Resources\Language\LanguageResource;
use App\Services\Languages\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Laravel\Models\MoonshineUser;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);
uses()->group('moonshine');

beforeEach(function (): void {
    app(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);

    $this->resource = app(LanguageResource::class);
    $this->user = MoonshineUser::factory()->create();
});

it('index page', function (): void {
    actingAs($this->user, 'moonshine')
        ->get($this->resource->getIndexPageUrl())
        ->assertOk();
});

it('create page', function (): void {
    actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl())
        ->assertOk();
});

it('edit page', function (): void {
    $item = Language::query()->where('code', 'en')->firstOrFail();

    actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl($item->getKey()))
        ->assertOk();
});

it('creates a new language through the form', function (): void {
    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.crud.store', ['resourceUri' => $this->resource->getUriKey()]), [
            'code' => 'de',
            'name' => 'Deutsch',
            'sort_order' => '2',
            'is_default' => '0',
        ])
        ->assertRedirect();

    expect(Language::query()->where('code', 'de')->exists())->toBeTrue();
});

it('rejects an invalid language code', function (): void {
    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.crud.store', ['resourceUri' => $this->resource->getUriKey()]), [
            'code' => 'not-a-valid-code',
            'name' => 'Broken',
            'sort_order' => '0',
            'is_default' => '0',
        ])
        ->assertInvalid(['code'], $this->resource->getUriKey());
});

it('switching the default language demotes others and refreshes the cache', function (): void {
    $en = Language::query()->where('code', 'en')->firstOrFail();

    actingAs($this->user, 'moonshine')
        ->patch(route('moonshine.crud.update', [
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $en->getKey(),
        ]), [
            'code' => 'en',
            'name' => 'English',
            'sort_order' => '1',
            'is_default' => '1',
        ])
        ->assertRedirect();

    expect($en->refresh()->is_default)->toBeTrue()
        ->and(Language::query()->where('code', 'ru')->first()->is_default)->toBeFalse()
        ->and(app(LanguageService::class)->defaultCode())->toBe('en');
});

it('cannot leave the system without a default language', function (): void {
    $ru = Language::query()->where('code', 'ru')->firstOrFail();

    actingAs($this->user, 'moonshine')
        ->patch(route('moonshine.crud.update', [
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $ru->getKey(),
        ]), [
            'code' => 'ru',
            'name' => 'Русский',
            'sort_order' => '0',
            'is_default' => '0',
        ])
        ->assertRedirect();

    expect($ru->refresh()->is_default)->toBeTrue()
        ->and(app(LanguageService::class)->defaultCode())->toBe('ru');
});
