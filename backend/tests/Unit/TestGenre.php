<?php

namespace Tests\Unit;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestGenre extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_genres(): void
    {
        $genre = Genre::factory()->create([
            'name_hu' => 'Fantasy HU',
            'name_en' => 'Fantasy',
        ]);

        $response = $this->getJson('/api/genres');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $genre->id)
            ->assertJsonPath('data.0.name_en', 'Fantasy');
    }

    public function test_show_returns_one_genre(): void
    {
        $genre = Genre::factory()->create([
            'name_hu' => 'Krimi',
            'name_en' => 'Crime',
        ]);

        $response = $this->getJson("/api/genres/{$genre->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $genre->id)
            ->assertJsonPath('data.name_hu', 'Krimi');
    }

    public function test_store_creates_genre(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/genres', [
            'name_hu' => 'Sci-fi',
            'name_en' => 'Science Fiction',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name_hu', 'Sci-fi')
            ->assertJsonPath('data.name_en', 'Science Fiction');

        $this->assertDatabaseHas('genres', [
            'name_hu' => 'Sci-fi',
            'name_en' => 'Science Fiction',
        ]);
    }

    public function test_update_changes_genre(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name_hu' => 'Regi nev',
            'name_en' => 'Old Name',
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/genres/{$genre->id}", [
            'name_hu' => 'Uj nev',
            'name_en' => 'New Name',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name_hu', 'Uj nev')
            ->assertJsonPath('data.name_en', 'New Name');

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name_hu' => 'Uj nev',
            'name_en' => 'New Name',
        ]);
    }

    public function test_destroy_deletes_genre(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/genres/{$genre->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('genres', ['id' => $genre->id]);
    }

}
