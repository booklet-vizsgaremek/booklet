<?php

namespace Database\Factories;

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
             "id" => fake()->uuid(),
            "img" => "./img/" . fake()->word() . ".img",
            "name" => fake()->firstName() . " " . fake()->lastName(),
            "author" => fake()->numberBetween(0, 10), // majd kell itt változtatni
            "price" => fake()->randomNumber(2),
            "pages" => fake()->randomNumber(3),
            "xp" => fake()->randomNumber(2),
            "cr" => fake()->randomNumber(2),
            "in_storage" => fake()->numberBetween(0, 100),
            "status" => fake()->randomElement(["accepted", "pending", "rejected"]),
            "publisher_id" => fake()->numberBetween(1, 10),
            "genre_id" => fake()->numberBetween(1, 10),
            "added_at" => now()
        ];
    }
}
