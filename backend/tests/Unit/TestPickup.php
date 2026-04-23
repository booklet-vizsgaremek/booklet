<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Pickup;
use App\Models\Publisher;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestPickup extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_pickups(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $receipt = $this->createReceipt($user);
        $pickup = Pickup::factory()->create([
            'receipt_id' => $receipt->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/pickups');

        $response->assertOk()
            ->assertJsonFragment(['id' => $pickup->id]);
    }

    public function test_show_returns_one_pickup(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $receipt = $this->createReceipt($user);
        $pickup = Pickup::factory()->create([
            'receipt_id' => $receipt->id,
            'status' => 'ready',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/pickups/{$pickup->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $pickup->id)
            ->assertJsonPath('data.status', 'ready');
    }

    public function test_store_pickup_is_forbidden_by_request_authorize(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $receipt = $this->createReceipt($user);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/pickups', [
            'receipt_id' => $receipt->id,
            'status' => 'pending',
        ]);

        $response->assertForbidden();
    }

    public function test_update_changes_pickup_status(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $receipt = $this->createReceipt($user);
        $pickup = Pickup::factory()->create([
            'receipt_id' => $receipt->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/pickups/{$pickup->id}", [
            'status' => 'completed',
            'completed_at' => now()->toDateTimeString(),
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('pickups', [
            'id' => $pickup->id,
            'status' => 'completed',
        ]);
    }

    public function test_destroy_soft_deletes_pickup(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $receipt = $this->createReceipt($user);
        $pickup = Pickup::factory()->create(['receipt_id' => $receipt->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/pickups/{$pickup->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('pickups', ['id' => $pickup->id]);
    }

    private function createReceipt(User $user): Receipt
    {
        $publisher = Publisher::factory()->create();
        $genre = Genre::factory()->create();
        $author = Author::factory()->create();
        $book = Book::factory()->create([
            'publisher_id' => $publisher->id,
            'genre_id' => $genre->id,
        ]);
        $book->authors()->attach($author->id);

        $receipt = Receipt::create([
            'user_id' => $user->id,
            'date' => now()->subDay(),
        ]);
        $receipt->books()->attach($book->id, ['quantity' => 1, 'price_at_purchase' => $book->price]);

        return $receipt;
    }
}
