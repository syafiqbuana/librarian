<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class DashboardController extends Controller
{
    public function index()
    {
        $latestBooks = Book::with('genres')->latest()->take(5)->get();
        $allBooks = Book::with('genres')->get();
        
        return view('dashboard', compact('latestBooks', 'allBooks'));
    }

    public function detailBook(Book $book){
        
        $book->load('genres');

        return view ('books.detail',compact('book'));
    }
}
