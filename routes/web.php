<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CartController;

use App\Http\Controllers\BorrowingController;
Route::get('/', function () {
    return view('/auth/login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

 Route::get('/cart', [CartController::class,'index'])->name('cart');
 Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
 Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

 Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing');
 Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
 Route::post('/borrowing{id}/return', [BorrowingController::class, 'return'])->name('borrowing.return');
 Route::get('/books/{book}',[DashboardController::class,'detailBook'])->name('book.detail');



Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
