<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PageTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageTranslation>
 */
class PageTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locale' => 'ru',
            'title' => fake()->sentence(3),
            'meta_title' => fake()->optional()->sentence(4),
            'meta_description' => fake()->optional()->paragraph(),
            'content' => null,
        ];
    }

    /**
     * Russian translation.
     */
    public function ru(): static
    {
        return $this->state(fn (array $attributes): array => [
            'locale' => 'ru',
        ]);
    }

    /**
     * English translation.
     */
    public function en(): static
    {
        return $this->state(fn (array $attributes): array => [
            'locale' => 'en',
        ]);
    }

    /**
     * Translation with flexible-layouts blocks in content.
     *
     * @param  array<int, array<string, mixed>>  $blocks
     */
    public function withBlocks(array $blocks): static
    {
        return $this->state(fn (array $attributes): array => [
            'content' => $blocks,
        ]);
    }
}
