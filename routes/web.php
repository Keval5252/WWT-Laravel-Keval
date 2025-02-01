<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'loginIndex']);
    Route::get('register', [AuthController::class, 'registerIndex']);

    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [WelcomeController::class, 'index']);
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('categories', [CategoriesController::class, 'index'])->name('categorie');
    Route::get('fetch-categories', [CategoriesController::class, 'fetchCategories'])->name('categories.fetch');
    Route::post('categories/store', [CategoriesController::class, 'store'])->name('categories.store');
    Route::get('categories/edit/{id}', [CategoriesController::class, 'edit'])->name('categories.edit');
    Route::post('categories/update/{id}', [CategoriesController::class, 'update'])->name('categories.update');
    Route::delete('categories/destroy/{id}', [CategoriesController::class, 'destroy'])->name('categories.destroy');

    Route::get('products', [ProductController::class, 'index'])->name('products');
    Route::get('products/fetch', [ProductController::class, 'fetchProducts'])->name('products.fetch');
    Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('products/update/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/destroy/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});
