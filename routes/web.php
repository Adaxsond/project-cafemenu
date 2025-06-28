<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;

/*
|--------------------------------------------------------------------------
| HALAMAN PELANGGAN
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return 'Selamat Datang di Menu Cafe.'; });
Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::get('/cart', [OrderController::class, 'cart'])->name('order.cart');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/{order}/status', [OrderController::class, 'status'])->name('order.status'); // <-- RUTE BARU

/*
|--------------------------------------------------------------------------
| HALAMAN ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders/{order}', [AdminController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
});

/*
|--------------------------------------------------------------------------
| RUTE BAWAAN BREEZE
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';