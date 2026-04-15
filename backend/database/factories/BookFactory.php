<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'img_path' => null,
            'title' => fake()->sentence(3),
            'price' => fake()->numberBetween(500, 5000),
            'pages' => fake()->numberBetween(50, 1000),
            'stock' => fake()->numberBetween(0, 100),
            'release_year' => fake()->numberBetween(1800, now()->year),
            'publisher_id' => Publisher::inRandomOrder()->value('id'),
            'genre_id' => Genre::inRandomOrder()->value('id'),
        ];
    }
}
