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
    expect(SettingsResolver::header())->toBe(['topBar' => null, 'nav' => null])
        ->and(SettingsResolver::footer())->toBe([
            'contacts' => null,
            'socials' => null,
            'columns' => [],
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
            ['platform' => 'Instagram', 'url' => 'https://instagram.com/bronber'],
        ])
        ->and($footer['columns'])->toBe([
            ['title' => 'Покупателям', 'links' => [
                ['label' => 'Доставка', 'href' => url('/delivery')],
            ]],
        ])
        ->and($footer['bottom']['copyright'])->toBe('© 2026 Bronber')
        ->and($footer['bottom']['privacy_label'])->toBe('Политика')
        ->and($footer['bottom']['developer_label'])->toBeNull();
});

it('keeps missing blocks as null while others resolve', function (): void {
    Setting::factory()->footer()->ru()->withBlocks([
        ['_type' => 'contacts', 'phone' => '+7 000', 'email' => null],
    ])->create();

    $footer = SettingsResolver::footer();

    expect($footer['contacts'])->toBe(['phone' => '+7 000', 'email' => null])
        ->and($footer['socials'])->toBeNull()
        ->and($footer['columns'])->toBe([])
        ->and($footer['bottom'])->toBeNull();
});
