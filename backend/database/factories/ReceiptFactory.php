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
            'user_id' => User::where('role', 'customer')->inRandomOrder()->value('id'),
            'date' => fake()->dateTimeBetween('-1 year', 'now')
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function ($receipt) {
            $books = Book::with('genre')->inRandomOrder()->take(fake()->numberBetween(1, 5))->get();
            $receipt->books()->attach(
                $books->mapWithKeys(fn($b) => [
                    $b->id => ['quantity' => rand(1, 3), 'price_at_purchase' => $b->price]
                ])->toArray()
            );

            if ($receipt->user_id) {
                $bookIds = $books->pluck('id');
                $genreIds = $books->pluck('genre.id');

                $coupon = Coupon::where(function ($query) use ($receipt, $bookIds, $genreIds) {
                    $query->where('user_id', $receipt->user_id)
                        ->whereNotNull('code');
                })
                    ->orWhere(function ($query) use ($bookIds, $genreIds) {
                        $query->whereNull('code')
                            ->where(function ($q) use ($bookIds, $genreIds) {
                                $q->whereIn('book_id', $bookIds)
                                    ->orWhereIn('genre_id', $genreIds);
                            });
                    })
                    ->inRandomOrder()
                    ->first();

                if ($coupon) $receipt->coupons()->attach($coupon->id);
                $receipt->pickup()->create(['status' => 'pending']);
            }
        });
    }
}
