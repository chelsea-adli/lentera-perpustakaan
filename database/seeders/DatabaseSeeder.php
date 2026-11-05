<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin
        User::updateOrCreate(
            ['email' => 'admin@lentera.test'],
            [
                'name' => 'Admin LENTERA',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Akun siswa
        User::updateOrCreate(
            ['email' => 'siswa@lentera.test'],
            [
                'name' => 'Siswa LENTERA',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
            ]
        );

        // Kategori buku
        $categories = [
            'Fiksi',
            'Teknologi',
            'Pendidikan',
            'Sejarah',
            'Umum',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        // Buku contoh
        $books = [
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'category' => 'Fiksi',
                'stock' => 5,
                'publisher' => 'Bentang Pustaka',
            ],
            [
                'title' => 'Pemrograman Web Dasar',
                'author' => 'Tim LENTERA',
                'category' => 'Teknologi',
                'stock' => 8,
                'publisher' => 'LENTERA',
            ],
            [
                'title' => 'Belajar Basis Data',
                'author' => 'Tim LENTERA',
                'category' => 'Pendidikan',
                'stock' => 6,
                'publisher' => 'LENTERA',
            ],
        ];

        foreach ($books as $data) {
            $category = Category::where(
                'name',
                $data['category']
            )->firstOrFail();

            Book::firstOrCreate(
                ['title' => $data['title']],
                [
                    'category_id' => $category->id,
                    'author' => $data['author'],
                    'publisher' => $data['publisher'],
                    'stock' => $data['stock'],
                ]
            );
        }
    }
}