<?php

namespace Database\Factories;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pickup>
 */
class PickupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'ready', 'completed', 'cancelled']);

        return [
            'receipt_id' => Receipt::inRandomOrder()->value('id'),
            'status' => $status,
            'completed_at' => $status === 'completed' ? fake()->dateTimeBetween('-1 month', 'now') : null
        ];
    }
}
