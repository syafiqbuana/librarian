<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\{Borrowing, BorrowingDetail, Book};
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{

    public function index()
    {
        $borrowings = Borrowing::with('borrowingDetail.book')->where('user_id', auth()->id())->get();

        return view('borrowing', compact('borrowings'));
    }
    public function store()
    {
        $cart = session('cart', []);

        // 1. validasi cart
        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong!');
        }

        try {
            DB::transaction(function () use ($cart) {

                $borrowing = Borrowing::create([
                    'user_id' => auth()->id(),
                    'borrow_date' => Carbon::now(),
                    'due_date' => Carbon::now()->addDays(14),
                    'status' => 'waiting',
                ]);

                foreach ($cart as $bookId => $item) {

                    $book = Book::findOrFail($bookId);
                    $qty = $item['quantity'];

                    // validasi stok
                    if ($book->stock < $qty) {
                        throw new \Exception("Stok buku {$book->title} tidak cukup");
                    }

                    // simpan detail
                    BorrowingDetail::create([
                        'borrowing_id' => $borrowing->id,
                        'book_id' => $book->id,
                        'quantity' => $qty,
                        
                    ]);

                    // kurangi stok
                }
            });

            // 7. clear cart if succes
            session()->forget('cart');

            return redirect()->route('borrowing')
                ->with('success', 'Peminjaman berhasil!');

        } catch (\Exception $e) {

            // 8. rollback if if failed
             dd($e->getMessage());
        }
    }

    public function return($id){

            $borrowing = Borrowing::findOrFail($id);

            if ($borrowing->isReturned() || $borrowing->isReturnedLate()) {
                return back()->with('error', 'Buku sudah dikembalikan!');
            }

            DB::transaction(function() use ($borrowing){
                $borrowing->update([
                    
                    'status' => 'pending_return',

                ]);
                //return stock
            });

            return back()->with('success', 'Pengajuan pengembalian berhasil!');
    }
}
