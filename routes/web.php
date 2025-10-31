<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
Route::get('/', function () {
    return view('welcome');
});


Route::resource('products', ProductController::class);

Route::patch('products/{product}/increase', [ProductController::class, 'increaseQuantity'])->name('products.increase');
Route::patch('products/{product}/decrease', [ProductController::class, 'decreaseQuantity'])->name('products.decrease');