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

        $user = auth()->user();

        $activeBorrowings = $user->borrowings()
            ->whereIn('status', ['borrowed', 'pending_return'])
            ->count();

        $returnedBorrowings = $user->borrowings()
            ->whereIn('status', ['returned', 'returned_late'])
            ->count();

        $overdueBorrowings = $user->borrowings()
            ->where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();

        $totalFine = $user->borrowings()
            ->whereIn('status', ['returned_late', 'returned'])
            ->get()
            ->sum('fine');

        return view('dashboard', compact(
            'latestBooks',
            'allBooks',
            'activeBorrowings',
            'returnedBorrowings',
            'overdueBorrowings',
            'totalFine'
        ));
    }

    public function detailBook(Book $book)
    {

        $book->load('genres');

        return view('books.detail', compact('book'));
    }
}
