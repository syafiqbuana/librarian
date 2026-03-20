<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            ['name' => 'Fiction', 'description' => 'Fictional works that are created from the imagination.'],
            ['name' => 'Non-Fiction', 'description' => 'Works based on facts, real events, and real people.'],
            ['name' => 'Science Fiction', 'description' => 'Fiction that explores futuristic concepts and advanced technology.'],
            ['name' => 'Fantasy', 'description' => 'Fiction that contains magical or supernatural elements.'],
            ['name' => 'Mystery', 'description' => 'Fiction that involves solving a crime or uncovering secrets.'],
            ['name' => 'Romance', 'description' => 'Fiction that focuses on romantic relationships between characters.'],
            ['name' => 'Thriller', 'description' => 'Fiction that is characterized by excitement, suspense, and tension.'],
            ['name' => 'Historical Fiction', 'description' => 'Fiction set in a specific historical period, often with real historical figures.'],
            ['name' => 'Biography', 'description' => 'Non-fiction works that tell the life story of a real person.'],
            ['name' => 'Self-Help', 'description' => 'Non-fiction works that provide advice and strategies for personal improvement.'],
        ];

        foreach ($genres as $genre) {
            \App\Models\Genre::firstOrCreate($genre);
        }
    }
}
