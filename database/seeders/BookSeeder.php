<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'book_code' => 'BK001',
                'title' => 'Harry Potter and the Philosopher\'s Stone',
                'author' => 'J.K. Rowling',
                'publisher' => 'Bloomsbury',
                'publication_year' => 1997,
                'isbn' => '9780747532699',
                'stock' => 10,
                'cover_image' => 'covers/hp1.jpg',
                'description' => 'A young wizard discovers his magical destiny.',
                'rack_location' => 'A1',
            ],
            [
                'book_code' => 'BK002',
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'publisher' => 'George Allen & Unwin',
                'publication_year' => 1937,
                'isbn' => '9780618968633',
                'stock' => 7,
                'cover_image' => 'covers/hobbit.jpg',
                'description' => 'Bilbo Baggins goes on an unexpected adventure.',
                'rack_location' => 'A2',
            ],
            [
                'book_code' => 'BK003',
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'publisher' => 'Prentice Hall',
                'publication_year' => 2008,
                'isbn' => '9780132350884',
                'stock' => 5,
                'cover_image' => 'covers/cleancode.jpg',
                'description' => 'A handbook of agile software craftsmanship.',
                'rack_location' => 'B1',
            ],
            [
                'book_code' => 'BK004',
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'publisher' => 'Scribner',
                'publication_year' => 1925,
                'isbn' => '9780743273565',
                'stock' => 8,
                'cover_image' => 'covers/gatsby.jpg',
                'description' => 'A novel about the American dream and excess.',
                'rack_location' => 'A3',
            ],
            [
                'book_code' => 'BK005',
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'publisher' => 'J.B. Lippincott & Co.',
                'publication_year' => 1960,
                'isbn' => '9780061120084',
                'stock' => 12,
                'cover_image' => 'covers/mockingbird.jpg',
                'description' => 'A novel about racial injustice in the Deep South.',
                'rack_location' => 'A4',
            ],

        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}