<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Genre;

class BookGenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $book = Book::where('id', 1)->firstOrCreate();

        $fantasyGenre = Genre::where('name', 'Fantasy')->first();
        $scienceFictionGenre = Genre::where('name', 'Science Fiction')->first();

        $book->genres()->attach([$fantasyGenre->id, $scienceFictionGenre->id]);
    }
}
