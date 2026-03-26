<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Receipt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $receipts = Receipt::factory(15)->create();
        $books = Book::all();

        foreach ($receipts as $receipt) {
            $selectedBooks = $books->random(rand(1, 3));
            foreach ($selectedBooks as $book) {
                DB::table('books_receipts')->insert([
                    'receipt_id' => $receipt->id,
                    'book_id' => $book->id,
                    'quantity' => rand(1, 5),
                    'price_at_purchase' => $book->price
                ]);
            }
        }
    }
}
