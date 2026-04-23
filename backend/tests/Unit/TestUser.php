<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TestUser extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('message_en', 'User with email john.doe@example.com has signed up successfully.');

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_self_returns_authenticated_user(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'email' => 'jane@example.com',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/users/self');

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', 'jane@example.com');
    }

    public function test_admin_index_returns_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'first_name' => 'Listed',
            'email' => 'listed@example.com',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/users');

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $user->id,
                'first_name' => 'Listed',
                'email' => 'listed@example.com',
            ]);
    }
}
