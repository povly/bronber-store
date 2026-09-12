<?php

use App\Models\Language;
use App\Models\Setting;
use App\Services\Languages\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    app(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
});

it('renders the static header and footer when no settings exist', function (): void {
    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('header__top')
        ->toContain('footer__brand')
        ->not->toContain('header--custom')
        ->not->toContain('footer--custom');
});

it('renders header blocks from settings instead of the static header', function (): void {
    Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'nav', 'links' => [['label' => 'Каталог', 'url' => '/catalog']]],
        ['_type' => 'contacts', 'phone' => '+7 (495) 000-00-00', 'email' => 'shop@bronber.ru'],
    ])->create();

    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('header--custom')
        ->toContain('pb-nav')
        ->toContain('Каталог')
        ->toContain('pb-header-contacts')
        ->not->toContain('header__top');
});

it('renders footer blocks from settings instead of the static footer', function (): void {
    Setting::factory()->footer()->ru()->withBlocks([
        ['_type' => 'copyright', 'text' => '© 2026 Bronber'],
        ['_type' => 'socials', 'links' => [['platform' => 'Telegram', 'url' => 'https://t.me/bronber']]],
    ])->create();

    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('footer--custom')
        ->toContain('pb-socials')
        ->toContain('pb-copyright')
        ->toContain('© 2026 Bronber')
        ->toContain('Telegram')
        ->not->toContain('footer__brand');
});

it('falls back to the default language setting for a non-default locale', function (): void {
    Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'nav', 'links' => [['label' => 'Русская навигация', 'url' => '/catalog']]],
    ])->create();

    $html = $this->get('/en')->getContent();

    expect($html)
        ->toContain('header--custom')
        ->toContain('Русская навигация');
});

it('renders static markup again when settings exist but are empty', function (): void {
    Setting::factory()->header()->ru()->create();

    $html = $this->get('/')->getContent();

    expect($html)
        ->toContain('header__top')
        ->not->toContain('header--custom');
});
