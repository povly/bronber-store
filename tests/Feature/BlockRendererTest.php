<?php

use App\Support\PageBlocks\BlockRenderer;
use Illuminate\Support\Facades\Log;

it('renders each block type to its matching view', function (array $block, string $expectedClass) {
    $html = app(BlockRenderer::class)->render([$block], 'page');

    expect($html)->toContain($expectedClass);
})->with([
    'hero' => [['_type' => 'hero'], 'pb-hero'],
    'text' => [['_type' => 'text'], 'pb-text'],
    'gallery' => [['_type' => 'gallery', 'images' => ['/images/x.png', 'media/y.jpg']], 'pb-gallery'],
    'faq' => [['_type' => 'faq', 'items' => [['question' => 'Q', 'answer' => 'A']]], 'pb-faq'],
    'contacts' => [['_type' => 'contacts', 'address' => 'Москва'], 'pb-contacts'],
    'featured-products' => [['_type' => 'featured-products'], 'pb-featured'],
]);

it('passes block data to the view', function () {
    $html = app(BlockRenderer::class)->render([
        ['_type' => 'hero', 'title' => 'Запчасти для BMW', 'subtitle' => 'Подбор по VIN'],
    ], 'page');

    expect($html)->toContain('Запчасти для BMW')
        ->and($html)->toContain('Подбор по VIN');
});

it('renders header and footer contexts with their own views', function () {
    $renderer = app(BlockRenderer::class);

    $header = $renderer->render([['_type' => 'nav', 'links' => [
        ['label' => 'Каталог', 'url' => '/catalog'],
    ]]], 'header');

    $footer = $renderer->render([['_type' => 'copyright', 'text' => '© 2026 Bronber']], 'footer');

    expect($header)->toContain('pb-nav')
        ->and($header)->toContain('Каталог')
        ->and($footer)->toContain('© 2026 Bronber');
});

it('skips unknown block types without failing and logs a warning', function () {
    Log::spy();

    $html = app(BlockRenderer::class)->render([
        ['_type' => 'unknown-block'],
        ['_type' => 'faq', 'items' => [['question' => 'Вопрос', 'answer' => 'Ответ']]],
    ], 'page');

    expect($html)->toContain('pb-faq')
        ->and($html)->not->toContain('unknown-block');

    Log::shouldHaveReceived('warning')->once();
});

it('skips malformed blocks', function () {
    $html = app(BlockRenderer::class)->render([
        'not-an-array',
        ['no_type_key' => true],
    ], 'page');

    expect($html)->toBe('');
});

it('renders featured products from the catalog mock', function () {
    $html = app(BlockRenderer::class)->render([
        ['_type' => 'featured-products', 'title' => 'Рекомендуем', 'count' => 2],
    ], 'page');

    expect($html)->toContain('Рекомендуем')
        ->and(substr_count($html, 'card--'))->toBeGreaterThanOrEqual(2);
});

it('renders an empty string for an empty block list', function () {
    expect(app(BlockRenderer::class)->render([], 'page'))->toBe('');
});
