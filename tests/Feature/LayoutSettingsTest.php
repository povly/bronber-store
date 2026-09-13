<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Setting;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\PageOptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();
    PageOptions::flush();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);

    // The home route renders strictly from the DB (no prototype
    // fallback) — the header/footer assertions need a rendered page.
    Page::factory()
        ->has(PageTranslation::factory()->ru()->state(['content' => []]), 'translations')
        ->create(['slug' => 'index']);

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

it('renders media from settings: logos, payment, social and contact icons', function (): void {
    File::ensureDirectoryExists(storage_path('app/public/settings-test'));
    File::put(storage_path('app/public/settings-test/logo.svg'), '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8" data-test="settings-logo"><rect width="8" height="8" fill="#000"/></svg>');
    File::put(storage_path('app/public/settings-test/icon.svg'), '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8" data-test="settings-icon"><rect width="8" height="8" fill="#fff"/></svg>');

    try {
        Setting::factory()->header()->ru()->withBlocks([
            ['_type' => 'logo', 'image' => 'settings-test/logo.svg'],
        ])->create();

        Setting::factory()->footer()->ru()->withBlocks([
            ['_type' => 'logo', 'image' => 'settings-test/logo.svg'],
            ['_type' => 'payment', 'image' => 'settings-test/logo.svg'],
            ['_type' => 'contacts', 'items' => [
                ['icon' => 'settings-test/icon.svg', 'text' => '+7 (000) 000-00-00', 'href' => 'tel:+70000000000'],
            ]],
            ['_type' => 'socials', 'links' => [
                ['platform' => 'Telegram', 'url' => 'https://t.me/bronber', 'icon' => 'settings-test/icon.svg'],
            ]],
        ])->create();

        $html = $this->get('/')->getContent();

        expect($html)
            ->toContain('data-test="settings-logo"')
            ->toContain('data-test="settings-icon"')
            ->toContain('tel:+70000000000')
            ->toContain('https://t.me/bronber');
    } finally {
        File::deleteDirectory(storage_path('app/public/settings-test'));
    }
});

it('renders the static mobile menu and nav when no mobile settings exist', function (): void {
    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('mobile-menu__link-divider')
        ->toContain(__('store.mobile_home'))
        ->toContain(__('store.mobile_cart'));
});

it('substitutes mobile menu link groups with dividers from settings', function (): void {
    Setting::factory()->mobileMenu()->ru()->withBlocks([
        ['_type' => 'links', 'links' => [
            ['label' => 'Мобильная ссылка', 'type' => 'custom', 'url' => '/mobile-service'],
        ]],
        ['_type' => 'links', 'links' => [
            ['label' => 'Вторая группа', 'type' => 'custom', 'url' => '/second-group'],
        ]],
        ['_type' => 'contacts', 'items' => [
            ['icon' => '/images/icons/phone.svg', 'text' => '+7 (111) 222-33-44', 'href' => 'tel:+71112223344'],
        ]],
    ])->create();

    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('Мобильная ссылка')
        ->toContain('href="/mobile-service"')
        ->toContain('Вторая группа')
        ->toContain('tel:+71112223344')
        ->and(substr_count($html, 'mobile-menu__link-divider'))->toBe(1);
});

it('substitutes mobile nav items and keeps the functional catalog button', function (): void {
    File::ensureDirectoryExists(storage_path('app/public/settings-test'));
    File::put(storage_path('app/public/settings-test/nav-icon.svg'), '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8 8" data-test="mobile-nav-icon"><rect width="8" height="8" fill="#fff"/></svg>');

    try {
        Setting::factory()->mobileNav()->ru()->withBlocks([
            ['_type' => 'items', 'items' => [
                ['icon' => 'settings-test/nav-icon.svg', 'label' => 'Избранное', 'type' => 'custom', 'url' => '/favorites'],
                ['icon' => null, 'label' => 'Профиль', 'type' => 'custom', 'url' => '/profile'],
            ]],
        ])->create();

        $html = $this->get('/')->getContent();

        expect($html)
            ->toContain('Избранное')
            ->toContain('data-test="mobile-nav-icon"')
            ->toContain('catalogMenu.openMobile()')
            ->not->toContain(__('store.mobile_home'));
    } finally {
        File::deleteDirectory(storage_path('app/public/settings-test'));
    }
});
