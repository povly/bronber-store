<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'cover_pc' => fake()->optional()->filePath(),
            'cover_mb' => fake()->optional()->filePath(),
        ];
    }

    /**
     * Unpublished article (draft).
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
        ]);
    }
}
