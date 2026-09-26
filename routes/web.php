<?php

use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
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

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);

    // Purchases & Stock Inflow
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update', 'destroy']);

    // Customers & Due Ledger
    Route::get('/customers/ledger', [CustomerController::class, 'ledger'])->name('customers.ledger');
    Route::post('/customers/{customer}/collect-due', [CustomerController::class, 'collectDue'])->name('customers.collect_due');
    Route::resource('customers', CustomerController::class)->except(['create', 'show', 'edit']);

    // Cash Register & Shifts
    Route::get('/cash-register', [CashRegisterController::class, 'index'])->name('cash_register.index');
    Route::post('/cash-register/open', [CashRegisterController::class, 'open'])->name('cash_register.open');
    Route::post('/cash-register/{cashRegister}/close', [CashRegisterController::class, 'close'])->name('cash_register.close');
    Route::get('/cash-register/history', [CashRegisterController::class, 'history'])->name('cash_register.history');
});

require __DIR__.'/auth.php';
