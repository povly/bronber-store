<?php

use App\Models\Language;
use App\Models\Page;
use App\MoonShine\Resources\Page\PageResource;
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

    $this->resource = app(PageResource::class);
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
    $item = Page::factory()->create();

    actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl($item->getKey()))
        ->assertOk();
});

it('creates translations for every active language on save', function (): void {
    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.crud.store', ['resourceUri' => $this->resource->getUriKey()]), [
            'slug' => 'o-kompanii',
            'is_published' => '1',
            'sort_order' => '0',
        ])
        ->assertRedirect();

    $page = Page::query()->where('slug', 'o-kompanii')->firstOrFail();

    expect($page->translations->pluck('locale')->all())->toEqualCanonicalizing(['ru', 'en'])
        ->and($page->translations->firstWhere('locale', 'ru')->title)->toBe('o-kompanii');
});

it('adds translations for languages added after page creation', function (): void {
    $page = Page::factory()->create(['slug' => 'delivery']);

    actingAs($this->user, 'moonshine')
        ->patch(route('moonshine.crud.update', [
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $page->getKey(),
        ]), [
            'slug' => 'delivery',
            'is_published' => '1',
            'sort_order' => '0',
        ])
        ->assertRedirect();

    Language::factory()->create(['code' => 'de', 'sort_order' => 2]);

    actingAs($this->user, 'moonshine')
        ->patch(route('moonshine.crud.update', [
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $page->getKey(),
        ]), [
            'slug' => 'delivery',
            'is_published' => '1',
            'sort_order' => '0',
        ])
        ->assertRedirect();

    expect($page->refresh()->translations->pluck('locale')->all())->toEqualCanonicalizing(['ru', 'en', 'de']);
});

it('rejects a non-kebab slug', function (): void {
    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.crud.store', ['resourceUri' => $this->resource->getUriKey()]), [
            'slug' => 'Not A Slug',
            'is_published' => '1',
            'sort_order' => '0',
        ])
        ->assertInvalid(['slug'], $this->resource->getUriKey());

    expect(Page::query()->count())->toBe(0);
});

it('rejects a duplicate slug', function (): void {
    Page::factory()->create(['slug' => 'about']);

    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.crud.store', ['resourceUri' => $this->resource->getUriKey()]), [
            'slug' => 'about',
            'is_published' => '1',
            'sort_order' => '0',
        ])
        ->assertInvalid(['slug'], $this->resource->getUriKey());
});
