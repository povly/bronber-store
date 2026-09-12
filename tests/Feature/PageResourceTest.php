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

it('edit page renders for a page with translations without running out of memory', function (): void {
    // Regression: FlexibleLayouts must not be nested inside HasMany inline fields
    // (infinite recursion in field name generation). Translations are edited
    // through the related resource form instead.
    $page = Page::factory()->create(['slug' => 'with-translations']);
    $page->translations()->create(['locale' => 'ru', 'title' => 'С переводами']);

    $html = actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl($page->getKey()))
        ->assertOk()
        ->getContent();

    expect(strlen($html))->toBeLessThan(5_000_000);
});

it('shows a create button for translations on the page form', function (): void {
    $page = Page::factory()->create();

    actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl($page->getKey()))
        ->assertOk()
        ->assertSee('Добавить');
});

it('allows creating a translation for a language the page does not have yet', function (): void {
    $page = Page::factory()->create(['slug' => 'manual-translation']);
    $page->translations()->create(['locale' => 'ru', 'title' => 'Русский']);

    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.crud.store', ['resourceUri' => 'page-translation-resource']), [
            'page_id' => (string) $page->getKey(),
            'locale' => 'en',
            'title' => 'English version',
        ])
        ->assertRedirect();

    expect($page->refresh()->translations->pluck('locale')->all())->toEqualCanonicalizing(['ru', 'en']);
});

it('rejects a second translation for the same language of the page', function (): void {
    $page = Page::factory()->create(['slug' => 'duplicate-locale']);
    $page->translations()->create(['locale' => 'ru', 'title' => 'Русский']);

    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.crud.store', ['resourceUri' => 'page-translation-resource']), [
            'page_id' => (string) $page->getKey(),
            'locale' => 'ru',
            'title' => 'Дубликат',
        ])
        ->assertInvalid(['locale'], 'page-translation-resource');

    expect($page->translations()->where('locale', 'ru')->count())->toBe(1);
});

it('rejects a translation for an inactive language', function (): void {
    $page = Page::factory()->create(['slug' => 'inactive-locale']);

    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.crud.store', ['resourceUri' => 'page-translation-resource']), [
            'page_id' => (string) $page->getKey(),
            'locale' => 'fr',
            'title' => 'Français',
        ])
        ->assertInvalid(['locale'], 'page-translation-resource');
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
