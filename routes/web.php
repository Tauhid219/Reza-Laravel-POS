<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    // Units
    Route::resource('units', UnitController::class)->except(['create', 'show', 'edit']);

    // Products & Barcodes
    Route::get('/products/barcodes', [ProductController::class, 'printBarcode'])->name('products.barcode');
    Route::resource('products', ProductController::class);
});

require __DIR__.'/auth.php';
