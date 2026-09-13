<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\Laravel\Models\MoonshineUser;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);
uses()->group('moonshine');

beforeEach(function (): void {
    config()->set('moonshine.media_manager.disk', 'public');
    Storage::fake('public');

    $this->user = MoonshineUser::factory()->create();
});

it('uploads an image to the public disk without intervention errors', function (): void {
    // Regression: laravel/framework 13.31 requires intervention/image ^4.0
    // (ImageManager::usingDriver). The upload event chain (OptimizeUploadedImage
    // → ImageOptimizer) must run without "Call to undefined method" fatals.
    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.media.manager.upload'), [
            'dir' => '/',
            'files' => [UploadedFile::fake()->image('photo.jpg', 120, 80)],
        ])
        ->assertOk()
        ->assertJson(['status' => true]);

    Storage::disk('public')->assertExists('photo.jpg');
});

it('rejects a file with a disallowed extension', function (): void {
    actingAs($this->user, 'moonshine')
        ->post(route('moonshine.media.manager.upload'), [
            'dir' => '/',
            'files' => [UploadedFile::fake()->create('payload.exe', 16, 'application/octet-stream')],
        ])
        ->assertStatus(400)
        ->assertJson(['status' => false]);

    Storage::disk('public')->assertMissing('payload.exe');
});

it('blocks guests from uploading', function (): void {
    post(route('moonshine.media.manager.upload'), [
        'dir' => '/',
        'files' => [UploadedFile::fake()->image('photo.jpg', 10, 10)],
    ])->assertRedirect();

    Storage::disk('public')->assertMissing('photo.jpg');
});
