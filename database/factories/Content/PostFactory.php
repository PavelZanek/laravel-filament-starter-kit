<?php

declare(strict_types=1);

namespace Database\Factories\Content;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'slug' => fake()->unique()->slug(),
            'published_at' => fake()->boolean(80)
                ? fake()->dateTimeBetween('-1 year', '+1 month')
                : null,
            // 'content' => fake()->paragraphs(3, true),
            'content' => null,
        ];
    }
}
