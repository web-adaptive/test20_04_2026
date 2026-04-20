<?php

use App\Http\Controllers\Web\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/product/{id}', [PageController::class, 'productShow'])->name('product.show');

Route::get('/login', [PageController::class, 'login'])->name('login');

Route::prefix('admin')->group(function () {
    Route::get('/products', [PageController::class, 'adminProducts'])->name('admin.products');
    Route::get('/products/create', [PageController::class, 'adminProductCreate'])->name('admin.products.create');
    Route::get('/products/{id}/edit', [PageController::class, 'adminProductEdit'])->name('admin.products.edit');
});
