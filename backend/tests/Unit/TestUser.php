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

    public function test_update_password_changes_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/users/{$user->id}/password", [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk()
            ->assertJsonPath('message_en', 'Password successfully updated.');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin, 'sanctum')->patchJson("/api/users/{$user->id}/role", [
            'role' => 'staff',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.role', 'staff');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'staff',
        ]);
    }

    public function test_destroy_deletes_own_customer_user(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/users/{$user->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_customer_cannot_list_users(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/users');

        $response->assertForbidden();
    }

    public function test_admin_show_returns_one_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'first_name' => 'Shown',
            'email' => 'shown@example.com',
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson("/api/users/{$user->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', 'shown@example.com');
    }

    public function test_update_changes_user(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/users/{$user->id}", [
            'first_name' => 'New',
            'last_name' => 'Name',
            'email' => 'new@example.com',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.first_name', 'New')
            ->assertJsonPath('data.email', 'new@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'New',
            'email' => 'new@example.com',
        ]);




}
