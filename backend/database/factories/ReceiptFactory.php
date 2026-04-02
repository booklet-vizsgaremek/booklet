<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Receipt>
 */
class ReceiptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'date' => fake()->dateTimeBetween('-1 year', 'now')
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function ($receipt) {
            $books = Book::inRandomOrder()->take(fake()->numberBetween(1, 5))->get();

            $receipt->books()->attach(
                $books->mapWithKeys(fn($book) => [
                    $book->id => [
                        'quantity' => fake()->numberBetween(1, 3),
                        'price_at_purchase' => $book->price
                    ]
                ])->toArray()
            );

            if (fake()->boolean()) {
                $coupon = Coupon::inRandomOrder()->first();
                if ($coupon) {
                    $receipt->coupons()->attach($coupon->id);
                }
            }
        });
    }
}
