<?php

use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();
});

it('returns the translation for the requested locale', function (): void {
    $page = Page::factory()
        ->has(PageTranslation::factory()->ru(), 'translations')
        ->has(PageTranslation::factory()->en(), 'translations')
        ->create();

    expect($page->translation('ru')->locale)->toBe('ru')
        ->and($page->translation('en')->locale)->toBe('en');
});

it('falls back to ru when the requested locale is missing', function (): void {
    $page = Page::factory()
        ->has(PageTranslation::factory()->ru(), 'translations')
        ->create();

    expect($page->translation('en')->locale)->toBe('ru');
});

it('falls back to the first translation when ru is missing too', function (): void {
    $page = Page::factory()
        ->has(PageTranslation::factory()->en(), 'translations')
        ->create();

    expect($page->translation('ru')->locale)->toBe('en')
        ->and($page->translation()->locale)->toBe('en');
});

it('returns null when the page has no translations at all', function (): void {
    $page = Page::factory()->create();

    expect($page->translation('en'))->toBeNull();
});

it('uses the current application locale when none is passed', function (): void {
    $page = Page::factory()
        ->has(PageTranslation::factory()->ru(), 'translations')
        ->has(PageTranslation::factory()->en(), 'translations')
        ->create();

    app()->setLocale('en');

    expect($page->translation()->locale)->toBe('en');
});

it('scopes to published pages only', function (): void {
    Page::factory()->count(2)->create();
    Page::factory()->draft()->create();

    expect(Page::query()->published()->count())->toBe(2);
});

it('casts page translation content to an array', function (): void {
    $translation = PageTranslation::factory()
        ->withBlocks([['_type' => 'hero', 'title' => 'Привет']])
        ->for(Page::factory(), 'page')
        ->create();

    expect($translation->refresh()->content)->toBe([['_type' => 'hero', 'title' => 'Привет']]);
});
