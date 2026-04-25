<?php

namespace Tests\Unit;

use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestPublisher extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_publishers(): void
    {
        $publisher = Publisher::factory()->create(['name' => 'Penguin']);

        $response = $this->getJson('/api/publishers');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $publisher->id)
            ->assertJsonPath('data.0.name', 'Penguin');
    }

    public function test_show_returns_one_publisher(): void
    {
        $publisher = Publisher::factory()->create(['name' => 'HarperCollins']);

        $response = $this->getJson("/api/publishers/{$publisher->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $publisher->id)
            ->assertJsonPath('data.name', 'HarperCollins');
    }

    public function test_store_creates_publisher(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/publishers', [
            'name' => 'New Publisher',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'New Publisher');

        $this->assertDatabaseHas('publishers', ['name' => 'New Publisher']);
    }

    public function test_update_changes_publisher(): void
    {
        $user = User::factory()->create();
        $publisher = Publisher::factory()->create(['name' => 'Old Publisher']);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/publishers/{$publisher->id}", [
            'name' => 'Updated Publisher',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Publisher');

        $this->assertDatabaseHas('publishers', [
            'id' => $publisher->id,
            'name' => 'Updated Publisher',
        ]);
    }

    public function test_destroy_soft_deletes_publisher(): void
    {
        $user = User::factory()->create();
        $publisher = Publisher::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/publishers/{$publisher->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('publishers', ['id' => $publisher->id]);
    }
}
