<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Setting;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\PageOptions;
use App\Support\PageBlocks\SettingsResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();
    PageOptions::flush();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);

    app()->setLocale('ru');
});

function resolverPage(string $slug): void
{
    $page = Page::factory()->create(['slug' => $slug]);
    PageTranslation::factory()->ru()->for($page, 'page')->create(['title' => 'Страница '.$slug]);
}

it('returns nulls when no settings exist', function (): void {
    expect(SettingsResolver::header())->toBe(['topBar' => null, 'nav' => null, 'logo' => null])
        ->and(SettingsResolver::footer())->toBe([
            'logo' => null,
            'contacts' => null,
            'contactItems' => null,
            'socials' => null,
            'columns' => [],
            'payment' => null,
            'bottom' => null,
        ]);
});

it('resolves the header top bar and nav with two-type links', function (): void {
    resolverPage('about');

    Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'top-bar', 'phone' => '+7 000', 'links' => [
            ['label' => 'Доставка', 'type' => 'custom', 'url' => '/delivery'],
            ['label' => 'О нас', 'type' => 'page', 'page' => 'about'],
            ['label' => 'Битая', 'type' => 'page', 'page' => 'missing'],
        ]],
        ['_type' => 'nav', 'links' => [
            ['label' => 'Каталог', 'type' => 'custom', 'url' => '/catalog'],
        ]],
    ])->create();

    $header = SettingsResolver::header();

    expect($header['topBar'])->toBe([
        'phone' => '+7 000',
        'links' => [
            ['label' => 'Доставка', 'href' => '/delivery'],
            ['label' => 'О нас', 'href' => url('/about')],
        ],
    ])->and($header['nav'])->toBe([
        'links' => [
            ['label' => 'Каталог', 'href' => '/catalog'],
        ],
    ]);
});

it('localizes page links for a non-default locale', function (): void {
    resolverPage('about');

    Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'nav', 'links' => [
            ['label' => 'About', 'type' => 'page', 'page' => 'about'],
        ]],
    ])->create();

    app()->setLocale('en');

    expect(SettingsResolver::header()['nav'])->toBe([
        'links' => [
            ['label' => 'About', 'href' => url('/en/about')],
        ],
    ]);
});

it('resolves the footer structures', function (): void {
    resolverPage('delivery');

    Setting::factory()->footer()->ru()->withBlocks([
        ['_type' => 'contacts', 'phone' => '+7 000', 'email' => 'shop@bronber.ru'],
        ['_type' => 'socials', 'links' => [
            ['platform' => 'Instagram', 'url' => 'https://instagram.com/bronber'],
            ['platform' => '', 'url' => 'https://no-label.com'],
        ]],
        ['_type' => 'links-column', 'title' => 'Покупателям', 'links' => [
            ['label' => 'Доставка', 'type' => 'page', 'page' => 'delivery'],
        ]],
        ['_type' => 'bottom', 'copyright' => '© 2026 Bronber', 'privacy_label' => 'Политика', 'privacy_url' => '#'],
    ])->create();

    $footer = SettingsResolver::footer();

    expect($footer['contacts'])->toBe(['phone' => '+7 000', 'email' => 'shop@bronber.ru'])
        ->and($footer['socials'])->toBe([
            ['platform' => 'Instagram', 'url' => 'https://instagram.com/bronber', 'icon' => null],
        ])
        ->and($footer['columns'])->toBe([
            ['title' => 'Покупателям', 'links' => [
                ['label' => 'Доставка', 'href' => url('/delivery')],
            ]],
        ])
        ->and($footer['bottom']['copyright'])->toBe('© 2026 Bronber')
        ->and($footer['bottom']['links'])->toBe([['label' => 'Политика', 'href' => '#']])
        ->and($footer['bottom']['developer_label'])->toBeNull();
});

it('resolves the bottom row legal links from the json list', function (): void {
    resolverPage('privacy');

    Setting::factory()->footer()->ru()->withBlocks([
        ['_type' => 'bottom', 'links' => [
            ['label' => 'Политика', 'type' => 'page', 'page' => 'privacy'],
            ['label' => 'Terms', 'type' => 'custom', 'url' => 'https://example.com/terms'],
        ], 'copyright' => '© 2026'],
    ])->create();

    expect(SettingsResolver::footer()['bottom']['links'])->toBe([
        ['label' => 'Политика', 'href' => url('/privacy')],
        ['label' => 'Terms', 'href' => 'https://example.com/terms'],
    ]);
});

it('keeps missing blocks as null while others resolve', function (): void {
    Setting::factory()->footer()->ru()->withBlocks([
        ['_type' => 'contacts', 'phone' => '+7 000', 'email' => null],
    ])->create();

    $footer = SettingsResolver::footer();

    expect($footer['contacts'])->toBe(['phone' => '+7 000', 'email' => null])
        ->and($footer['logo'])->toBeNull()
        ->and($footer['contactItems'])->toBeNull()
        ->and($footer['socials'])->toBeNull()
        ->and($footer['columns'])->toBe([])
        ->and($footer['payment'])->toBeNull()
        ->and($footer['bottom'])->toBeNull();
});

it('resolves logo, payment, contact items and social icons with normalized media paths', function (): void {
    Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'logo', 'image' => 'brand/logo-header.svg'],
    ])->create();

    Setting::factory()->footer()->ru()->withBlocks([
        ['_type' => 'logo', 'image' => '/images/brand/logo-footer.svg'],
        ['_type' => 'contacts', 'items' => [
            ['icon' => 'icons/phone.svg', 'text' => '+7 000', 'href' => 'tel:+7000'],
            ['icon' => null, 'text' => 'Без иконки'],
            ['icon' => 'icons/mail.svg', 'text' => '', 'href' => 'mailto:x@y.z'],
        ]],
        ['_type' => 'socials', 'links' => [
            ['platform' => 'Telegram', 'url' => 'https://t.me/bronber', 'icon' => 'icons/tg.svg'],
        ]],
        ['_type' => 'payment', 'image' => 'brand/payment.png'],
    ])->create();

    $header = SettingsResolver::header();
    $footer = SettingsResolver::footer();

    expect($header['logo'])->toBe(['image' => '/storage/brand/logo-header.svg'])
        ->and($footer['logo'])->toBe(['image' => '/images/brand/logo-footer.svg'])
        ->and($footer['payment'])->toBe(['image' => '/storage/brand/payment.png'])
        ->and($footer['contactItems'])->toBe([
            ['icon' => '/storage/icons/phone.svg', 'text' => '+7 000', 'href' => 'tel:+7000'],
            ['icon' => null, 'text' => 'Без иконки', 'href' => null],
        ])
        ->and($footer['socials'])->toBe([
            ['platform' => 'Telegram', 'url' => 'https://t.me/bronber', 'icon' => '/storage/icons/tg.svg'],
        ])
        ->and($footer['contacts'])->toBe(['phone' => null, 'email' => null]);
});

it('inherits empty header/footer media from the default locale settings', function (): void {
    Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'logo', 'image' => 'brand/logo.svg'],
    ])->create();

    Setting::factory()->header()->en()->withBlocks([
        ['_type' => 'top-bar', 'phone' => '+7 EN'],
    ])->create();

    Setting::factory()->footer()->ru()->withBlocks([
        ['_type' => 'logo', 'image' => 'brand/logo.svg'],
        ['_type' => 'payment', 'image' => 'brand/pay.png'],
        ['_type' => 'socials', 'links' => [
            ['platform' => 'Telegram', 'url' => 'https://t.me/x', 'icon' => 'icons/tg.svg'],
        ]],
        ['_type' => 'contacts', 'items' => [
            ['icon' => 'icons/phone.svg', 'text' => 'RU телефон'],
        ]],
    ])->create();

    Setting::factory()->footer()->en()->withBlocks([
        ['_type' => 'socials', 'links' => [
            ['platform' => 'Telegram', 'url' => 'https://t.me/x', 'icon' => null],
        ]],
        ['_type' => 'contacts', 'items' => [
            ['icon' => null, 'text' => 'EN phone'],
        ]],
    ])->create();

    app()->setLocale('en');

    $header = SettingsResolver::header();
    $footer = SettingsResolver::footer();

    expect($header['logo'])->toBe(['image' => '/storage/brand/logo.svg'])
        ->and($header['topBar']['phone'])->toBe('+7 EN')
        ->and($footer['logo'])->toBe(['image' => '/storage/brand/logo.svg'])
        ->and($footer['payment'])->toBe(['image' => '/storage/brand/pay.png'])
        ->and($footer['socials'][0]['icon'])->toBe('/storage/icons/tg.svg')
        ->and($footer['contactItems'][0]['text'])->toBe('EN phone')
        ->and($footer['contactItems'][0]['icon'])->toBe('/storage/icons/phone.svg');
});
