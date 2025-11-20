<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
Route::get('/', function () {
    return view('welcome');
});


Route::resource('products', ProductController::class);

Route::post('products/{product}/increase-quantity', [ProductController::class, 'increaseQuantity'])->name('products.increase-quantity');
Route::post('products/{product}/decrease-quantity', [ProductController::class, 'decreaseQuantity'])->name('products.decrease-quantity');

Route::post('products/{product}/increment', [ProductController::class, 'increaseQuantity'])->name('products.increment');
Route::post('products/{product}/decrement', [ProductController::class, 'decreaseQuantity'])->name('products.decrement');