<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Seed the default language set: ru (default) + en.
     */
    public function run(): void
    {
        Language::query()->upsert([
            ['code' => 'ru', 'name' => 'Русский', 'sort_order' => 0, 'is_default' => true],
            ['code' => 'en', 'name' => 'English', 'sort_order' => 1, 'is_default' => false],
        ], 'code');
    }
}
