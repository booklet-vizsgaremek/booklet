<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use App\Models\Receipt;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestReceipt extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_receipts_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $receipt = Receipt::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/receipts');

        $response->assertOk()
            ->assertJsonFragment(['id' => $receipt->id]);
    }

    public function test_show_returns_one_receipt(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $receipt = Receipt::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/receipts/{$receipt->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $receipt->id);
    }

    public function test_store_creates_receipt_and_decrements_stock(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $book = $this->createBook(['stock' => 10, 'price' => 2000]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/receipts', [
            'user_id' => $user->id,
            'date' => now()->toDateTimeString(),
            'books' => [
                ['id' => $book->id, 'quantity' => 2],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.books.0.id', $book->id);

        $this->assertDatabaseHas('receipts', ['user_id' => $user->id]);
        $this->assertDatabaseHas('books_receipts', [
            'book_id' => $book->id,
            'quantity' => 2,
            'price_at_purchase' => 2000,
        ]);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'stock' => 8,
        ]);
    }

    public function test_update_changes_receipt_date(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $book = $this->createBook();
        $receipt = Receipt::create([
            'user_id' => $user->id,
            'date' => now()->subDay(),
        ]);
        $receipt->books()->attach($book->id, ['quantity' => 1, 'price_at_purchase' => $book->price]);

        $newDate = now()->toDateTimeString();

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/receipts/{$receipt->id}", [
            'user_id' => $user->id,
            'date' => $newDate,
            'books' => [
                ['id' => $book->id, 'quantity' => 1],
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $receipt->id);

        $this->assertDatabaseHas('receipts', [
            'id' => $receipt->id,
            'user_id' => $user->id,
            'date' => $newDate,
        ]);
    }

    public function test_destroy_deletes_receipt(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $receipt = Receipt::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/receipts/{$receipt->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('receipts', ['id' => $receipt->id]);
    }

    private function createBook(array $attributes = []): Book
    {
        $publisher = Publisher::factory()->create();
        $genre = Genre::factory()->create();
        $author = Author::factory()->create();

        $book = Book::factory()->create(array_merge([
            'publisher_id' => $publisher->id,
            'genre_id' => $genre->id,
        ], $attributes));

        $book->authors()->attach($author->id);

        return $book;
    }
}
