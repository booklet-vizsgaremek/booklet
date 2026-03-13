<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchasedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $book_uuid = DB::table('books')
            ->pluck('id');
        $user_uuid = DB::table('users')
            ->pluck('id');


        //dump($book_uuid);

        for ($i = 0; $i <= 10; $i++) {

            DB::table('purchased')->insert([
                "user_id" => fake()->randomElement($user_uuid),
                "book_id" => fake()->randomElement($book_uuid)
            ]);
        }
    }
}
