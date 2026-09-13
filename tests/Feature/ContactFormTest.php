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

/**
 * @return array<string, string>
 */
function contactPayload(): array
{
    return [
        'name' => 'Иван Тестов',
        'phone' => '+7 (985) 449-8000',
        'email' => 'ivan@example.test',
        'message' => 'Нужна запчасть на BMW X5',
    ];
}

it('accepts a valid submission with a success flash', function (): void {
    $this->post('/contacts', contactPayload())
        ->assertRedirect()
        ->assertSessionHas('success');
});

it('accepts a submission on the locale-prefixed route', function (): void {
    $this->post('/en/contacts', contactPayload())
        ->assertRedirect()
        ->assertSessionHas('success');
});

it('rejects an invalid submission', function (): void {
    $this->from('/contacts')->post('/contacts', [
        'name' => '',
        'phone' => '',
        'email' => 'not-an-email',
    ])->assertSessionHasErrors(['name', 'phone', 'email']);
});

it('rate limits the form submissions', function (): void {
    foreach (range(1, 10) as $i) {
        $this->post('/contacts', contactPayload())->assertRedirect();
    }

    $this->post('/contacts', contactPayload())->assertStatus(429);
});
