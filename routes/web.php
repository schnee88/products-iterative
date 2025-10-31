<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
Route::get('/', function () {
    return view('welcome');
});


Route::resource('products', ProductController::class);

Route::patch('products/{product}/increment', [ProductController::class, 'increaseQuantity'])->name('products.increment');
Route::patch('products/{product}/decrement', [ProductController::class, 'decreaseQuantity'])->name('products.decrement');