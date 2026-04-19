<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Genre;
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
        $isBookCoupon = fake()->boolean(50);
        $hasCode = fake()->boolean(10);

        return [
            'book_id' => $isBookCoupon ? Book::inRandomOrder()->value('id') : null,
            'genre_id' => !$isBookCoupon ? Genre::inRandomOrder()->value('id') : null,
            'user_id' =>  $hasCode ? User::where('role', 'customer')->inRandomOrder()->value('id') : null,
            'discount' => fake()->numberBetween(5, 50),
            'starts_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'ends_at' => fake()->dateTimeBetween('now', '+1 year'),
            'code' => $hasCode ? fake()->unique()->regexify('[A-Z0-9]{6}') : null
        ];
    }
}
