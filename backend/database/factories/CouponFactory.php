<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => Book::inRandomOrder()->value('id'),
            'genre_id' => null,
            'user_id' => User::inRandomOrder()->value('id'),
            'discount' => fake()->numberBetween(5, 50),
            'starts_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'ends_at' => fake()->dateTimeBetween('now', '+1 year'),
            'code' => fake()->unique()->regexify('[A-Z0-9]{6}')
        ];
    }
}
