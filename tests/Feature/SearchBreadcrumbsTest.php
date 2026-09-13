<?php

use App\Models\Language;
use App\Services\Languages\LanguageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);
});

it('shows the search title and a three-item breadcrumb trail', function (): void {
    $html = $this->get('/catalog?search=bosch')->assertOk()->getContent();

    expect($html)
        ->toContain('Поиск: bosch')
        ->toContain('BreadcrumbList')
        ->toContain(url('/'))
        ->toContain(url('/catalog'));

    expect(substr_count($html, 'ListItem'))->toBeGreaterThanOrEqual(3);
});

it('localizes the trail on the en catalog route', function (): void {
    $html = $this->get('/en/catalog?search=bosch')->assertOk()->getContent();

    expect($html)
        ->toContain('Search: bosch')
        ->toContain('Home')
        ->toContain(url('/en/'))
        ->toContain(url('/en/catalog'))
        ->not->toContain('Поиск:');
});

it('keeps the plain catalog trail without a search query', function (): void {
    $html = $this->get('/catalog')->assertOk()->getContent();

    expect($html)
        ->toContain('Каталог')
        ->not->toContain('Поиск:');

    expect(substr_count($html, 'ListItem'))->toBeGreaterThanOrEqual(2);
});
