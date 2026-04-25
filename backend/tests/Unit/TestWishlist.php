<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestWishlist extends TestCase
{
    use RefreshDatabase;



    
    public function test_index_returns_wishlist_books(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook(['title' => 'Wishlist Book']);
        $user->wishlists()->attach($book->id);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/wishlists');

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $book->id,
                'title' => 'Wishlist Book',
            ]);
    }

    public function test_store_adds_book_to_wishlist(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/wishlists', [
            'book_id' => $book->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $book->id);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_store_duplicate_wishlist_returns_no_content(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook();
        $user->wishlists()->attach($book->id);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/wishlists', [
            'book_id' => $book->id,
        ]);

        $response->assertNoContent();
    }

    public function test_destroy_removes_book_from_wishlist(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook();
        $user->wishlists()->attach($book->id);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/wishlists/{$book->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_guest_cannot_access_wishlist_index(): void
    {
        $response = $this->getJson('/api/wishlists');

        $response->assertUnauthorized();
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
