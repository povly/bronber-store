<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
});

function demoPage(): Page
{
    return Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'О компании',
            'meta_title' => 'О компании Bronber — автозапчасти',
            'meta_description' => 'Мы продаём топливные насосы',
            'meta_keywords' => 'насосы, автозапчасти',
            'content' => [['_type' => 'hero', 'title' => 'О компании', 'subtitle' => 'Автозапчасти']],
        ]), 'translations')
        ->has(PageTranslation::factory()->en()->state([
            'title' => 'About us',
            'meta_description' => 'We sell fuel pumps',
            'content' => [['_type' => 'hero', 'title' => 'About us', 'subtitle' => 'Auto parts']],
        ]), 'translations')
        ->create(['slug' => 'o-kompanii']);
}

it('renders a published page by slug', function (): void {
    demoPage();

    $response = $this->get('/o-kompanii');

    $response->assertOk()
        ->assertSee('О компании')
        ->assertSee('Автозапчасти')
        ->assertSee('pb-hero');
});

it('renders the en translation on the locale-prefixed route', function (): void {
    demoPage();

    $this->get('/en/o-kompanii')
        ->assertOk()
        ->assertSee('About us')
        ->assertSee('Auto parts');
});

it('falls back to the default language translation when locale is missing', function (): void {
    Page::factory()
        ->has(PageTranslation::factory()->ru()->state([
            'title' => 'Только русский',
            'content' => [['_type' => 'hero', 'title' => 'Только русский']],
        ]), 'translations')
        ->create(['slug' => 'ru-only']);

    $this->get('/en/ru-only')
        ->assertOk()
        ->assertSee('Только русский');
});

it('returns 404 for drafts and unknown slugs', function (): void {
    demoPage();
    Page::factory()->draft()->create(['slug' => 'draft-page']);

    $this->get('/draft-page')->assertNotFound();
    $this->get('/no-such-page')->assertNotFound();
    $this->get('/en/no-such-page')->assertNotFound();
});

it('does not shadow fixed prototype routes', function (): void {
    demoPage();

    $this->get('/catalog')->assertOk();
    $this->get('/en/catalog')->assertOk();
    $this->get('/cart')->assertOk();
});

it('renders the full SEO tag set from the current translation', function (): void {
    demoPage();

    $html = $this->get('/o-kompanii')->getContent();

    expect($html)
        ->toContain('<title>О компании Bronber — автозапчасти</title>')
        ->toContain('<meta name="description" content="Мы продаём топливные насосы">')
        ->toContain('<meta name="keywords" content="насосы, автозапчасти">')
        ->toContain('<meta name="robots" content="index, follow">')
        ->toContain('<link rel="canonical" href="'.url('/o-kompanii').'">')
        ->toContain('<meta property="og:title" content="О компании Bronber — автозапчасти">')
        ->toContain('<meta property="og:description" content="Мы продаём топливные насосы">')
        ->toContain('<meta property="og:url" content="'.url('/o-kompanii').'">')
        ->toContain('<meta name="twitter:card" content="summary">')
        ->toContain('<link rel="alternate" hreflang="ru" href="'.url('/o-kompanii').'">')
        ->toContain('<link rel="alternate" hreflang="en" href="'.url('/en/o-kompanii').'">')
        ->toContain('<link rel="alternate" hreflang="x-default" href="'.url('/o-kompanii').'">');
});

it('keeps the default title on prototype pages', function (): void {
    $html = $this->get('/')->getContent();

    expect($html)->toContain('<title>Bronber Store</title>');
});
