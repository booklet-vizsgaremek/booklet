<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Publisher;
use App\Models\Genre;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Author::factory(10)->create();
        Publisher::factory(5)->create();
        Genre::factory(10)->create();

        User::factory(7)->create();
        User::create([
            'first_name' => 'Staff',
            'last_name' => 'User',
            'email' => 'staff@staff.com',
            'email_verified_at' => now(),
            'password' => Hash::make(env('DB_ROOT_PASSWORD')),
            'role' => 'staff'
        ]);
        User::create([
            'first_name' => 'Manager',
            'last_name' => 'User',
            'email' => 'manager@manager.com',
            'email_verified_at' => now(),
            'password' => Hash::make(env('DB_ROOT_PASSWORD')),
            'role' => 'manager'
        ]);
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make(env('DB_ROOT_PASSWORD')),
            'role' => 'admin'
        ]);

        Book::factory(20)->create()->each(function ($book) {
            $authorIds = Author::inRandomOrder()->limit(rand(1, 3))->pluck('id');
            $book->authors()->sync($authorIds);
        });

        $this->call([
            WishlistSeeder::class,
            ReceiptSeeder::class
        ]);
    }
}
