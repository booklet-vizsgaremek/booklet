<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestAuthor extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_authors(): void
    {
        $author = Author::factory()->create([
            'name' => 'Robert C. Martin',
        ]);

        $response = $this->getJson('/api/authors');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $author->id)
            ->assertJsonPath('data.0.name', 'Robert C. Martin');
    }

    public function test_show_returns_one_author(): void
    {
        $author = Author::factory()->create([
            'name' => 'J. K. Rowling',
            'biography_en' => 'English biography',
            'biography_hu' => 'Magyar eletrajz',
        ]);

        $response = $this->getJson("/api/authors/{$author->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $author->id)
            ->assertJsonPath('data.name', 'J. K. Rowling')
            ->assertJsonPath('data.biography_en', 'English biography');
    }

    public function test_store_creates_author(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/authors', [
            'name' => 'New Author',
            'biography_en' => 'New English biography',
            'biography_hu' => 'Uj magyar eletrajz',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'New Author')
            ->assertJsonPath('data.biography_hu', 'Uj magyar eletrajz');

        $this->assertDatabaseHas('authors', [
            'name' => 'New Author',
            'biography_en' => 'New English biography',
            'biography_hu' => 'Uj magyar eletrajz',
        ]);
    }

    public function test_update_changes_author(): void
    {
        $user = User::factory()->create();
        $author = Author::factory()->create([
            'name' => 'Old Author',
            'biography_en' => 'Old biography',
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/authors/{$author->id}", [
            'name' => 'Updated Author',
            'biography_en' => 'Updated biography',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Author')
            ->assertJsonPath('data.biography_en', 'Updated biography');

        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'name' => 'Updated Author',
            'biography_en' => 'Updated biography',
        ]);
    }

    public function test_destroy_deletes_author(): void
    {
        $user = User::factory()->create();
        $author = Author::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/authors/{$author->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('authors', ['id' => $author->id]);
    }
}
