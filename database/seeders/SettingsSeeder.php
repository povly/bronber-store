<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\Languages\LanguageService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed header/footer settings for every active language (ru default + en).
     * Safe to re-run: upsert on (key, locale), existing block values untouched.
     */
    public function run(): void
    {
        $languages = resolve(LanguageService::class)->codes();

        $rows = [];

        foreach (['header', 'footer'] as $key) {
            foreach ($languages as $language) {
                $rows[] = [
                    'key' => $key,
                    'locale' => $language,
                    'value' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Setting::query()->upsert($rows, ['key', 'locale'], ['updated_at']);
    }
}
