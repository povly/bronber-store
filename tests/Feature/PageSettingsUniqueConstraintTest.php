<?php

use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Setting;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rejects duplicate page slugs', function (): void {
    Page::factory()->create(['slug' => 'about']);

    Page::factory()->create(['slug' => 'about']);
})->throws(UniqueConstraintViolationException::class);

it('rejects duplicate translation for the same page and locale', function (): void {
    $page = Page::factory()->create();

    PageTranslation::factory()->for($page, 'page')->ru()->create();
    PageTranslation::factory()->for($page, 'page')->ru()->create();
})->throws(UniqueConstraintViolationException::class);

it('allows the same locale on different pages', function (): void {
    PageTranslation::factory()->ru()->for(Page::factory(), 'page')->create();
    PageTranslation::factory()->ru()->for(Page::factory(), 'page')->create();

    expect(PageTranslation::query()->count())->toBe(2);
});

it('rejects duplicate setting key and locale pair', function (): void {
    Setting::factory()->header()->ru()->create();

    Setting::factory()->header()->ru()->create();
})->throws(UniqueConstraintViolationException::class);

it('allows the same key for different locales', function (): void {
    Setting::factory()->header()->ru()->create();
    Setting::factory()->header()->en()->create();

    expect(Setting::query()->count())->toBe(2);
});
