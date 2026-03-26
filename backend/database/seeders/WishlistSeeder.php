<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $user) {
            $selectedBooks = $books->random(rand(1, 4));
            foreach ($selectedBooks as $book) {
                DB::table('wishlists')->insertOrIgnore([
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
