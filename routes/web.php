<?php

use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
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

    // POS Terminal
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // Sales & Orders Management
    Route::get('/orders/due', [OrderController::class, 'dueOrders'])->name('orders.due');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::resource('orders', OrderController::class)->only(['index', 'show']);

    // Reports & Analytics
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit_loss');
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/customer-due', [ReportController::class, 'customerDue'])->name('reports.customer_due');

    // System Administration & Settings (Admin Only)
    Route::group(['middleware' => ['role:Admin']], function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::resource('users', UserController::class);
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });
});

require __DIR__.'/auth.php';
