<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Setting;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\PageOptions;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();
    PageOptions::flush();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);

    app()->setLocale('ru');
});

it('renders the static header and footer when no settings exist', function (): void {
    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('header__nav')
        ->toContain('top-bar__links--right')
        ->toContain('footer__columns')
        ->not->toContain('data-from-settings');
});

it('substitutes top bar links and phone from settings', function (): void {
    Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'top-bar', 'phone' => '+7 (111) 222-33-44', 'links' => [
            ['label' => 'Сервисная ссылка', 'type' => 'custom', 'url' => '/service'],
        ]],
    ])->create();

    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('+7 (111) 222-33-44')
        ->toContain('href="/service"')
        ->toContain('Сервисная ссылка')
        ->not->toContain('top-bar__links--right');
});

it('substitutes main nav links from settings inside the static markup', function (): void {
    Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'nav', 'links' => [
            ['label' => 'Наш блог', 'type' => 'custom', 'url' => '/blog'],
        ]],
    ])->create();

    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('header__nav-right')
        ->toContain('Наш блог')
        ->not->toContain('class="header__nav-link">'.__('store.nav_new'));
});

it('renders a localized page link in the nav', function (): void {
    $page = Page::factory()->create(['slug' => 'about-us']);
    PageTranslation::factory()->ru()->for($page, 'page')->create(['title' => 'О нас']);

    Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'nav', 'links' => [
            ['label' => 'О нас', 'type' => 'page', 'page' => 'about-us'],
        ]],
    ])->create();

    $html = $this->get('/en')->getContent();

    expect($html)
        ->toContain('О нас')
        ->toContain('href="'.url('/en/about-us').'"');
});

it('substitutes footer contacts, columns and bottom from settings', function (): void {
    Setting::factory()->footer()->ru()->withBlocks([
        ['_type' => 'contacts', 'phone' => '+7 (555) 000-11-22', 'email' => 'shop@bronber.ru'],
        ['_type' => 'links-column', 'title' => 'Покупателям', 'links' => [
            ['label' => 'Доставка', 'type' => 'custom', 'url' => '/delivery'],
        ]],
        ['_type' => 'bottom', 'copyright' => '© 2026 Bronber Test', 'privacy_label' => 'Конфиденциальность', 'privacy_url' => '/privacy'],
    ])->create();

    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('+7 (555) 000-11-22')
        ->toContain('shop@bronber.ru')
        ->toContain('Покупателям')
        ->toContain('© 2026 Bronber Test')
        ->toContain('Конфиденциальность')
        ->not->toContain(__('store.footer_cat_engine'));
});

it('falls back to static markup when settings exist but are empty', function (): void {
    Setting::factory()->header()->ru()->create();

    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain(__('store.nav_new'))
        ->toContain('top-bar__links--right');
});
