<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(LanguageSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(HomePageSeeder::class);
        $this->call(FaqPageSeeder::class);
        $this->call(DeliveryPageSeeder::class);
        $this->call(ContactsPageSeeder::class);
        $this->call(AboutPageSeeder::class);
        $this->call(ReturnsPageSeeder::class);
        $this->call(LoyaltyPageSeeder::class);

        if (! app()->environment('production') && User::query()->doesntExist()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
