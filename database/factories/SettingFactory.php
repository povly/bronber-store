<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'header',
            'locale' => 'ru',
            'value' => null,
        ];
    }

    /**
     * Header setting.
     */
    public function header(): static
    {
        return $this->state(fn (array $attributes): array => [
            'key' => 'header',
        ]);
    }

    /**
     * Footer setting.
     */
    public function footer(): static
    {
        return $this->state(fn (array $attributes): array => [
            'key' => 'footer',
        ]);
    }

    /**
     * Mobile drawer menu setting.
     */
    public function mobileMenu(): static
    {
        return $this->state(fn (array $attributes): array => [
            'key' => 'mobile-menu',
        ]);
    }

    /**
     * Mobile bottom nav setting.
     */
    public function mobileNav(): static
    {
        return $this->state(fn (array $attributes): array => [
            'key' => 'mobile-nav',
        ]);
    }

    /**
     * Russian locale.
     */
    public function ru(): static
    {
        return $this->state(fn (array $attributes): array => [
            'locale' => 'ru',
        ]);
    }

    /**
     * English locale.
     */
    public function en(): static
    {
        return $this->state(fn (array $attributes): array => [
            'locale' => 'en',
        ]);
    }

    /**
     * Setting with flexible-layouts blocks in value.
     *
     * @param  array<int, array<string, mixed>>  $blocks
     */
    public function withBlocks(array $blocks): static
    {
        return $this->state(fn (array $attributes): array => [
            'value' => $blocks,
        ]);
    }
}
