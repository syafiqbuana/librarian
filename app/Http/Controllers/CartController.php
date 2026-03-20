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

        if (isset($cart[$id])) {

            if ($cart[$id]['quantity'] >= $book->stock) {
                return back()->with('error', 'Stok tidak mencukupi');
            }

            $cart[$id]['quantity']++;

        } else {

            $cart[$id] = [
                'title' => $book->title,
                'quantity' => 1,
            ];
        }

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
