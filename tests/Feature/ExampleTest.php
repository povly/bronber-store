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

    // The home route renders strictly from the DB (no prototype fallback).
    Page::factory()
        ->has(PageTranslation::factory()->ru()->state(['content' => []]), 'translations')
        ->create(['slug' => 'index']);
});

test('the application returns a successful response', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);
});
