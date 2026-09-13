<?php

use App\Models\Language;
use App\Models\Setting;
use App\MoonShine\Resources\Setting\SettingResource;
use App\Services\Languages\LanguageService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Laravel\Models\MoonshineUser;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);
uses()->group('moonshine');

beforeEach(function (): void {
    resolve(LanguageService::class)->clearCache();

    Language::factory()->default()->create(['code' => 'ru', 'sort_order' => 0]);
    Language::factory()->create(['code' => 'en', 'sort_order' => 1]);

    $this->resource = resolve(SettingResource::class);
    $this->user = MoonshineUser::factory()->create();
});

it('index page', function (): void {
    actingAs($this->user, 'moonshine')
        ->get($this->resource->getIndexPageUrl())
        ->assertOk();
});

it('edit page', function (): void {
    $item = Setting::factory()->header()->ru()->create();

    actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl($item->getKey()))
        ->assertOk();
});

it('renders media blocks (logo, payment) in the footer settings form', function (): void {
    $item = Setting::factory()->footer()->ru()->withBlocks([
        ['_type' => 'payment', 'image' => 'footer/payment.svg'],
        ['_type' => 'logo', 'image' => 'footer/logo.svg'],
    ])->create();

    actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl($item->getKey()))
        ->assertOk();
});

it('adds the payment block via the flexible-layouts ajax endpoint', function (): void {
    $item = Setting::factory()->footer()->ru()->create();

    $html = actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl($item->getKey()))
        ->assertOk()
        ->getContent();

    preg_match('/flexibleLayouts\(\s*`([^`]+)`/s', $html, $m);
    $addRoute = $m[1] ?? null;

    expect($addRoute)->not->toBeNull();

    $response = actingAs($this->user, 'moonshine')
        ->post($addRoute, [
            'field' => 'value',
            'path' => 'value',
            'name' => 'payment',
            'counts' => [],
        ])
        ->assertOk();

    expect($response->json('blockHtml'))->not->toBeNull();
});

it('saves the payment block through the settings form', function (): void {
    $item = Setting::factory()->footer()->ru()->create();

    actingAs($this->user, 'moonshine')
        ->patch(route('moonshine.crud.update', [
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $item->getKey(),
        ]), [
            'key' => 'footer',
            'locale' => 'ru',
            'value' => [
                ['_type' => 'payment', 'image' => 'footer/payment.svg'],
                ['_type' => 'logo', 'image' => 'footer/logo.svg'],
            ],
        ])
        ->assertRedirect();

    expect($item->refresh()->value)->toBe([
        ['_type' => 'payment', 'image' => 'footer/payment.svg'],
        ['_type' => 'logo', 'image' => 'footer/logo.svg'],
    ]);
});

it('loads the link type visibility script on the settings form', function (): void {
    $item = Setting::factory()->header()->ru()->withBlocks([
        ['_type' => 'top-bar', 'links' => [
            ['label' => 'Доставка', 'type' => 'custom', 'url' => '/delivery'],
        ]],
    ])->create();

    $html = actingAs($this->user, 'moonshine')
        ->get($this->resource->getFormPageUrl($item->getKey()))
        ->assertOk()
        ->getContent();

    expect($html)
        ->toContain('/vendor/bronber/link-type-visibility.js')
        ->toContain('[type]');
});

it('saves flexible-layouts blocks through the form', function (): void {
    $item = Setting::factory()->header()->ru()->create();

    actingAs($this->user, 'moonshine')
        ->patch(route('moonshine.crud.update', [
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $item->getKey(),
        ]), [
            'key' => 'header',
            'locale' => 'ru',
            'value' => [
                ['_type' => 'top-bar', 'phone' => '+7 (985) 449-80-00', 'links' => [
                    ['label' => 'Доставка', 'type' => 'custom', 'page' => null, 'url' => '/delivery'],
                ]],
                ['_type' => 'nav', 'links' => [
                    ['label' => 'О нас', 'type' => 'page', 'page' => 'o-kompanii', 'url' => null],
                ]],
            ],
        ])
        ->assertRedirect();

    expect($item->refresh()->value)->toBe([
        ['_type' => 'top-bar', 'phone' => '+7 (985) 449-80-00', 'links' => [
            ['label' => 'Доставка', 'type' => 'custom', 'page' => null, 'url' => '/delivery'],
        ]],
        ['_type' => 'nav', 'links' => [
            ['label' => 'О нас', 'type' => 'page', 'page' => 'o-kompanii', 'url' => null],
        ]],
    ]);
});

it('keeps key and locale unchanged on save', function (): void {
    $item = Setting::factory()->footer()->en()->create();

    actingAs($this->user, 'moonshine')
        ->patch(route('moonshine.crud.update', [
            'resourceUri' => $this->resource->getUriKey(),
            'resourceItem' => $item->getKey(),
        ]), [
            'key' => 'header',
            'locale' => 'ru',
            'value' => null,
        ])
        ->assertRedirect();

    expect($item->refresh()->key)->toBe('footer')
        ->and($item->refresh()->locale)->toBe('en');
});

it('seeds header, footer and mobile settings with prototype content for every active language', function (): void {
    $this->seed(SettingsSeeder::class);

    $rows = Setting::query()->get()
        ->map(fn (Setting $setting): string => $setting->key.'.'.$setting->locale)
        ->all();

    expect($rows)->toEqualCanonicalizing([
        'header.ru', 'header.en',
        'footer.ru', 'footer.en',
        'mobile-menu.ru', 'mobile-menu.en',
        'mobile-nav.ru', 'mobile-nav.en',
    ]);

    $headerRu = Setting::query()->where('key', 'header')->where('locale', 'ru')->first();

    expect($headerRu->value)->not->toBeNull()
        ->and(collect($headerRu->value)->pluck('_type')->all())->toEqual(['top-bar', 'nav'])
        ->and($headerRu->value[0]['links'][0])->toBe([
            'label' => 'Доставка',
            'type' => 'custom',
            'page' => null,
            'url' => '/delivery',
        ]);

    $footerEn = Setting::query()->where('key', 'footer')->where('locale', 'en')->first();

    expect(collect($footerEn->value)->pluck('_type')->all())->toEqual(['contacts', 'socials', 'links-column', 'links-column', 'links-column', 'bottom'])
        ->and($footerEn->value[2]['links'][0]['url'])->toBe('/en/catalog');

    $mobileMenuRu = Setting::query()->where('key', 'mobile-menu')->where('locale', 'ru')->first();

    expect(collect($mobileMenuRu->value)->pluck('_type')->all())->toEqual(['links', 'contacts'])
        ->and($mobileMenuRu->value[0]['links'][0]['url'])->toBe('/blog');

    $mobileNavEn = Setting::query()->where('key', 'mobile-nav')->where('locale', 'en')->first();

    expect($mobileNavEn->value)->toBeNull();
});

it('seeder is re-runnable and does not duplicate rows', function (): void {
    $this->seed(SettingsSeeder::class);
    $this->seed(SettingsSeeder::class);

    expect(Setting::query()->count())->toBe(8);
});

it('seeder does not overwrite admin-edited values', function (): void {
    Setting::query()->create([
        'key' => 'header',
        'locale' => 'ru',
        'value' => [['_type' => 'nav', 'links' => [['label' => 'Админская ссылка', 'type' => 'custom', 'url' => '/custom']]]],
    ]);

    $this->seed(SettingsSeeder::class);

    $header = Setting::query()->where('key', 'header')->where('locale', 'ru')->first();

    expect($header->value)->toBe([['_type' => 'nav', 'links' => [['label' => 'Админская ссылка', 'type' => 'custom', 'url' => '/custom']]]]);
});

it('seeder fills previously empty values', function (): void {
    Setting::query()->create(['key' => 'footer', 'locale' => 'ru', 'value' => null]);

    $this->seed(SettingsSeeder::class);

    $footer = Setting::query()->where('key', 'footer')->where('locale', 'ru')->first();

    expect($footer->value)->not->toBeNull()
        ->and(collect($footer->value)->pluck('_type')->first())->toBe('contacts');
});
