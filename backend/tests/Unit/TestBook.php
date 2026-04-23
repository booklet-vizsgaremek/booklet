<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestBook extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_books(): void
    {
        $book = $this->createBook(['title' => 'Clean Code']);

        $response = $this->getJson('/api/books');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $book->id)
            ->assertJsonPath('data.0.title', 'Clean Code');
    }

    public function test_show_returns_one_book(): void
    {
        $book = $this->createBook(['title' => 'Laravel Basics']);

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.title', 'Laravel Basics');
    }

    public function test_store_creates_book(): void
    {
        $user = User::factory()->create();
        $author = Author::factory()->create();
        $publisher = Publisher::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/books', [
            'title' => 'New Book',
            'author_ids' => [$author->id],
            'price' => 2500,
            'pages' => 320,
            'release_year' => 2024,
            'stock' => 8,
            'publisher_id' => $publisher->id,
            'genre_id' => $genre->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'New Book');

        $this->assertDatabaseHas('books', ['title' => 'New Book']);
        $this->assertDatabaseHas('author_book', ['author_id' => $author->id]);
    }

    public function test_update_changes_book(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook(['title' => 'Old Title', 'price' => 1000]);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/books/{$book->id}", [
            'title' => 'Updated Title',
            'price' => 1500,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Updated Title')
            ->assertJsonPath('data.price', 1500);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title',
            'price' => 1500,
        ]);
    }

    public function test_destroy_deletes_book(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/books/{$book->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
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
