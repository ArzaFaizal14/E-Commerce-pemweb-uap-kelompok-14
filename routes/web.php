<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StoreRegistrationController;
use App\Http\Controllers\SellerProductController;
use App\Http\Controllers\StoreProfileController;
use App\Http\Controllers\SellerOrderController;
use App\Http\Controllers\StoreBalanceController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\StoreVerificationController;

// -------------------------------
// PUBLIC ROUTES (Tidak perlu login)
// -------------------------------
Route::get('/', function () {
    return view('welcome');
});

// -------------------------------
// PROFILE ROUTES (Bawaan Breeze)
// -------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -------------------------------
// MEMBER ROUTES (harus login & role:member)
// -------------------------------
Route::middleware(['auth', 'role:member'])->group(function () {

    // Dashboard (list produk customer)
    Route::get('/dashboard', [ProductController::class, 'index'])
        ->name('dashboard');

    // Product Detail Page (customer)
    Route::get('/products/{slug}', [ProductController::class, 'show'])
        ->name('products.show');

    // Checkout
    Route::get('/checkout/{product}', [CheckoutController::class, 'create'])
        ->name('checkout.show');

    Route::post('/checkout/{product}', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    // Riwayat transaksi (list)
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    // Detail transaksi
    Route::get('/transactions/{code}', [TransactionController::class, 'show'])
        ->name('transactions.show');

    // Store Registration Page
    Route::get('/store/registration', [StoreRegistrationController::class, 'create'])
        ->name('store.registration');

    Route::post('/store/registration', [StoreRegistrationController::class, 'store'])
        ->name('store.registration.store');

    // ---------------------------------
    // SELLER ROUTES (toko milik member)
    // ---------------------------------
    Route::prefix('seller')->name('seller.')->group(function () {

        // Seller Product CRUD (pakai SellerProductController)
        Route::resource('products', SellerProductController::class)
            ->except(['show']);

        // Manage Product Images
        Route::get('/products/{product}/images', [SellerProductController::class, 'images'])
            ->name('products.images');

        Route::post('/products/{product}/images', [SellerProductController::class, 'storeImage'])
            ->name('products.images.store');

        Route::delete('/products/{product}/images/{image}', [SellerProductController::class, 'destroyImage'])
            ->name('products.images.destroy');

        // Store Profile (update/delete profil toko)
        Route::get('/store/profile', [StoreProfileController::class, 'edit'])
            ->name('store.profile.edit');

        Route::put('/store/profile', [StoreProfileController::class, 'update'])
            ->name('store.profile.update');

        Route::delete('/store/profile', [StoreProfileController::class, 'destroy'])
            ->name('store.profile.destroy');

        // Order Management (pesanan masuk ke toko)
        Route::get('/orders', [SellerOrderController::class, 'index'])
            ->name('orders.index');

        Route::get('/orders/{transaction}', [SellerOrderController::class, 'show'])
            ->name('orders.show');

        Route::put('/orders/{transaction}/status', [SellerOrderController::class, 'updateStatus'])
            ->name('orders.status.update');

        Route::put('/orders/{transaction}/tracking', [SellerOrderController::class, 'updateTracking'])
            ->name('orders.tracking.update');

        // Store Balance (lihat saldo + riwayat saldo)
        Route::get('/balance', [StoreBalanceController::class, 'index'])
            ->name('balance.index');

        // Withdrawals (ajukan penarikan saldo)
        Route::get('/withdrawals', [WithdrawalController::class, 'index'])
            ->name('withdrawals.index');

        Route::post('/withdrawals', [WithdrawalController::class, 'store'])
            ->name('withdrawals.store');
    });
});

// -------------------------------
// ADMIN ROUTES (Owner of e-commerce)
// -------------------------------
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Store Verification Page
        Route::get('/stores/verifications', [StoreVerificationController::class, 'index'])
            ->name('stores.verifications.index');

        Route::put('/stores/{store}/verify', [StoreVerificationController::class, 'verify'])
            ->name('stores.verify');

        Route::delete('/stores/{store}/reject', [StoreVerificationController::class, 'reject'])
            ->name('stores.reject');

        // User & Store Management Page
        Route::get('/users-stores', [AdminManagementController::class, 'index'])
            ->name('users-stores.index');

        Route::delete('/users/{user}', [AdminManagementController::class, 'destroyUser'])
            ->name('users.destroy');

        Route::delete('/stores/{store}', [AdminManagementController::class, 'destroyStore'])
            ->name('stores.destroy');
    });


require __DIR__.'/auth.php';
