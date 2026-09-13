<?php

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Services\Languages\LanguageService;
use App\Support\PageBlocks\MediaFallback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
});

it('fills empty media fields from the default locale content', function (): void {
    $content = [
        ['_type' => 'home-partners', 'title' => 'Our partners'],
        ['_type' => 'home-advs', 'items' => [
            ['_type' => 'item', 'title' => 'Fast'],
            ['_type' => 'item', 'title' => 'Easy', 'image' => '/images/en/easy.svg'],
        ]],
    ];

    $fallback = [
        ['_type' => 'home-partners', 'title' => 'Наши партнеры', 'images' => '["home/partners/brembo.png"]'],
        ['_type' => 'home-advs', 'items' => [
            ['_type' => 'item', 'title' => 'Быстро', 'image' => 'home/advs/fast.svg'],
            ['_type' => 'item', 'title' => 'Удобно', 'image' => 'home/advs/easy.svg'],
        ]],
    ];

    $result = MediaFallback::apply($content, $fallback);

    expect($result[0]['images'])->toBe('["home/partners/brembo.png"]')
        ->and($result[1]['items'][0]['image'])->toBe('home/advs/fast.svg')
        ->and($result[1]['items'][1]['image'])->toBe('/images/en/easy.svg')
        ->and($result[0]['title'])->toBe('Our partners');
});

it('keeps non-empty media of the current locale', function (): void {
    $result = MediaFallback::apply(
        [['_type' => 'home-partners', 'images' => ['en/logo.png']]],
        [['_type' => 'home-partners', 'images' => '["ru/logo.png"]']],
    );

    expect($result[0]['images'])->toBe(['en/logo.png']);
});

it('matches blocks by type and occurrence order', function (): void {
    $result = MediaFallback::apply(
        [
            ['_type' => 'home-partners', 'images' => '[]'],
            ['_type' => 'home-products', 'title' => 'A'],
            ['_type' => 'home-partners', 'images' => '["en/second.png"]'],
        ],
        [
            ['_type' => 'home-partners', 'images' => '["ru/first.png"]'],
            ['_type' => 'home-partners', 'images' => '["ru/second.png"]'],
        ],
    );

    expect($result[0]['images'])->toBe('["ru/first.png"]')
        ->and($result[2]['images'])->toBe('["en/second.png"]');
});

it('returns content unchanged for the default translation', function (): void {
    $content = [['_type' => 'home-partners']];

    expect(MediaFallback::apply($content, null))->toBe($content);
});

it('treats various empty shapes as empty media', function (): void {
    $fallback = [['_type' => 'home-partners', 'images' => '["ru/logo.png"]']];

    foreach ([null, '', [], '[]', 'not-json'] as $empty) {
        $block = ['_type' => 'home-partners'];

        if ($empty !== null) {
            $block['images'] = $empty;
        }

        $result = MediaFallback::apply([$block], $fallback);

        expect($result[0]['images'])->toBe('["ru/logo.png"]');
    }
});

it('ignores malformed blocks and missing fallback counterparts', function (): void {
    $result = MediaFallback::apply(
        ['not-an-array', ['_type' => 'home-news'], ['no-type' => true]],
        [['_type' => 'home-news', 'items' => [['image' => 'ru/news.svg']]]],
    );

    expect($result)->toBe(['not-an-array', ['_type' => 'home-news'], ['no-type' => true]]);
});

it('renders partner logos from the default language when the en translation has none', function (): void {
    File::ensureDirectoryExists(storage_path('app/public/home/partners-test'));
    File::put(storage_path('app/public/home/partners-test/logo.png'), 'png');

    try {
        Page::factory()
            ->has(PageTranslation::factory()->ru()->state([
                'title' => 'Партнёры',
                'meta_description' => 'Партнёры Bronber',
                'content' => [
                    ['_type' => 'home-partners', 'title' => 'Наши партнеры', 'images' => '["home/partners-test/logo.png"]'],
                ],
            ]), 'translations')
            ->has(PageTranslation::factory()->en()->state([
                'title' => 'Partners',
                'meta_description' => 'Bronber partners',
                'content' => [
                    ['_type' => 'home-partners', 'title' => 'Our partners', 'images' => '[]'],
                ],
            ]), 'translations')
            ->create(['slug' => 'partners']);

        $html = $this->get('/en/partners')->getContent();

        expect($html)->toContain('Our partners')
            ->and($html)->toContain('/storage/home/partners-test/logo');
    } finally {
        File::deleteDirectory(storage_path('app/public/home/partners-test'));
    }
});

it('fills empty link icons by index', function (): void {
    $result = MediaFallback::apply(
        [['_type' => 'socials', 'links' => [
            ['platform' => 'TG', 'url' => '#', 'icon' => null],
            ['platform' => 'IG', 'url' => '#', 'icon' => 'en/ig.svg'],
        ]]],
        [['_type' => 'socials', 'links' => [
            ['platform' => 'TG', 'url' => '#', 'icon' => 'ru/tg.svg'],
            ['platform' => 'IG', 'url' => '#', 'icon' => 'ru/ig.svg'],
        ]]],
    );

    expect($result[0]['links'][0]['icon'])->toBe('ru/tg.svg')
        ->and($result[0]['links'][1]['icon'])->toBe('en/ig.svg');
});

it('inherits missing pure-media blocks and skips empty default ones', function (): void {
    $result = MediaFallback::apply(
        [['_type' => 'contacts', 'items' => [['text' => 'Phone']]]],
        [
            ['_type' => 'logo', 'image' => 'brand/logo.svg'],
            ['_type' => 'payment', 'image' => null],
            ['_type' => 'bottom', 'copyright' => '© RU'],
        ],
        ['logo', 'payment'],
    );

    expect($result)->toHaveCount(2)
        ->and($result[1])->toBe(['_type' => 'logo', 'image' => 'brand/logo.svg']);
});
