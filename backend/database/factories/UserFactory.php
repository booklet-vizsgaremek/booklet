<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            "id" => fake()->uuid(),
            'first_name' => fake()->unique()->safeEmail(),
            'last_name' => fake()->unique()->safeEmail(),
            'email' => fake()->unique()->email(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(["guest", "author", "curator", "admin"]),
            'xp' => fake()->numberBetween(0, 100),
            'cr' => fake()->numberBetween(0, 100),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
