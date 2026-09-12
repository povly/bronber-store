<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\LinkResolver;
use App\Support\PageBlocks\PageOptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();
    PageOptions::flush();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);

    app()->setLocale('ru');
});

function publishedPage(string $slug): Page
{
    $page = Page::factory()->create(['slug' => $slug]);
    PageTranslation::factory()->ru()->state(['title' => 'Страница '.$slug])->for($page, 'page')->create();
    PageTranslation::factory()->en()->state(['title' => 'Page '.$slug])->for($page, 'page')->create();

    return $page;
}

it('resolves a page link for the default locale', function (): void {
    publishedPage('delivery');

    $href = LinkResolver::href(['type' => 'page', 'page' => 'delivery']);

    expect($href)->toBe(url('/delivery'));
});

it('resolves a page link for a non-default locale', function (): void {
    publishedPage('delivery');

    $href = LinkResolver::href(['type' => 'page', 'page' => 'delivery'], 'en');

    expect($href)->toBe(url('/en/delivery'));
});

it('resolves a page link using the current app locale', function (): void {
    publishedPage('delivery');

    app()->setLocale('en');

    expect(LinkResolver::href(['type' => 'page', 'page' => 'delivery']))->toBe(url('/en/delivery'));
});

it('returns null for an unpublished or unknown page link', function (): void {
    Log::spy();

    Page::factory()->draft()->create(['slug' => 'draft-page']);
    publishedPage('published-page');

    expect(LinkResolver::href(['type' => 'page', 'page' => 'draft-page']))->toBeNull()
        ->and(LinkResolver::href(['type' => 'page', 'page' => 'no-such-page']))->toBeNull()
        ->and(LinkResolver::href(['type' => 'page', 'page' => 'published-page']))->toBe(url('/published-page'));

    Log::shouldHaveReceived('warning')->twice();
});

it('resolves a custom link as-is', function (): void {
    expect(LinkResolver::href(['type' => 'custom', 'url' => '/catalog']))->toBe('/catalog')
        ->and(LinkResolver::href(['type' => 'custom', 'url' => '#!']))->toBe('#!')
        ->and(LinkResolver::href(['type' => 'custom', 'url' => 'https://example.com']))->toBe('https://example.com');
});

it('returns null for a custom link without url', function (): void {
    expect(LinkResolver::href(['type' => 'custom', 'url' => '']))->toBeNull()
        ->and(LinkResolver::href(['type' => 'custom']))->toBeNull();
});

it('returns null for an invalid link', function (): void {
    expect(LinkResolver::href([]))->toBeNull()
        ->and(LinkResolver::href(['type' => 'tel', 'url' => '+7']))->toBeNull();
});

it('lists published pages with localized titles', function (): void {
    publishedPage('delivery');
    Page::factory()->draft()->create(['slug' => 'hidden']);

    $options = PageOptions::published();

    expect($options)->toBe(['delivery' => 'Страница delivery'])
        ->and(PageOptions::isPublished('delivery'))->toBeTrue()
        ->and(PageOptions::isPublished('hidden'))->toBeFalse();

    app()->setLocale('en');
    PageOptions::flush();

    expect(PageOptions::published())->toBe(['delivery' => 'Page delivery']);
});
