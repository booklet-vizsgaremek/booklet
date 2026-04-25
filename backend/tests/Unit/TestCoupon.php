<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestCoupon extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_active_coupons(): void
    {
        $coupon = Coupon::factory()->create([
            'code' => null,
            'user_id' => null,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $response = $this->getJson('/api/coupons');

        $response->assertOk()
            ->assertJsonFragment(['id' => $coupon->id]);
    }

    public function test_show_returns_one_coupon(): void
    {
        $coupon = Coupon::factory()->create([
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $response = $this->getJson("/api/coupons/{$coupon->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $coupon->id)
            ->assertJsonPath('data.discount', $coupon->discount);
    }

    public function test_store_creates_coupon(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/coupons', [
            'book_id' => $book->id,
            'discount' => 15,
            'starts_at' => now()->subDay()->toDateTimeString(),
            'ends_at' => now()->addDay()->toDateTimeString(),
            'code' => 'SAVE15',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.discount', 15)
            ->assertJsonPath('data.code', 'SAVE15');

        $this->assertDatabaseHas('coupons', [
            'book_id' => $book->id,
            'discount' => 15,
            'code' => 'SAVE15',
        ]);
    }

    public function test_update_changes_coupon(): void
    {
        $user = User::factory()->create();
        $book = $this->createBook();
        $coupon = Coupon::factory()->create([
            'book_id' => $book->id,
            'genre_id' => null,
            'user_id' => null,
            'discount' => 10,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
            'code' => 'OLD10',
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/coupons/{$coupon->id}", [
            'book_id' => $coupon->book_id,
            'user_id' => $coupon->user_id,
            'discount' => 25,
            'starts_at' => now()->subDay()->toDateTimeString(),
            'ends_at' => now()->addDays(2)->toDateTimeString(),
            'code' => 'NEW25',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.discount', 25)
            ->assertJsonPath('data.code', 'NEW25');

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'discount' => 25,
            'code' => 'NEW25',
        ]);
    }

    public function test_destroy_soft_deletes_coupon(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/coupons/{$coupon->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
    }

    private function createBook(): Book
    {
        $publisher = Publisher::factory()->create();
        $genre = Genre::factory()->create();
        $author = Author::factory()->create();

        $book = Book::factory()->create([
            'publisher_id' => $publisher->id,
            'genre_id' => $genre->id,
        ]);

        $book->authors()->attach($author->id);

        return $book;
    }
}
