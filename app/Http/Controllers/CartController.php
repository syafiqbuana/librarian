<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
class CartController extends Controller
{
    public function index()
    {
        $carts = session()->get('cart', []);

        if (empty($carts)) {
            return view('cart', [
                'carts' => [],
                'books' => collect()
            ]);
        }

        $bookIds = array_keys($carts);
        $books = Book::whereIn('id', $bookIds)->get();

        return view('cart', compact('carts', 'books'));
    }

    public function add($id)
    {
        $book = Book::findOrFail($id);

        if ($book->stock < 1) {
            return back()->with('error', 'Stok buku habis');
        }

        $cart = session()->get('cart', []);

        // 🔥 Batasi maksimal 3 buku berbeda
        if (count($cart) >= 3 && !isset($cart[$id])) {
            return back()->with('error', 'Maksimal 3 buku dalam satu peminjaman');
        }

        // 🔥 Kalau buku sudah ada, tidak perlu tambah lagi
        if (isset($cart[$id])) {
            return back()->with('error', 'Buku sudah ada di cart');
        }

        // 🔥 Selalu quantity = 1
        $cart[$id] = [
            'title' => $book->title,
            'quantity' => 1,
        ];

        session()->put('cart', $cart);

        return back()->with('success', 'Buku berhasil ditambahkan ke cart');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Book removed from cart successfully!');
    }

    // public function update(){

    // }
}
