<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'book_code',
        'title',
        'author',
        'publisher',
        'publication_year',
        'isbn',
        'stock',
        'cover_image',
        'description',
        'rack_location',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'stock' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {

            $lastBook = self::latest()->first();

            $number = $lastBook ? intval(substr($lastBook->book_code, -4)) + 1 : 1;

            $book->book_code = 'BOOK-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        });
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genres');
    }

}
