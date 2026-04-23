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


}
