<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\BuyerProfileController;
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
use App\Http\Controllers\ProductCategoryController;



/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (tanpa login)
|--------------------------------------------------------------------------
*/

// Bisa pakai landing page biasa
Route::get('/', function () {
    return view('welcome');
})->name('home');


/*
|--------------------------------------------------------------------------
| AUTH ROUTES (bawaan Breeze / Jetstream)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| PROFILE ROUTES (bisa untuk semua user yang login: admin/member)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Dashboard member → list produk
    Route::get('/dashboard', [ProductController::class, 'index'])
        ->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| MEMBER ROUTES (user role: member)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:member'])->group(function () {
    // Detail produk
    Route::get('/products/{slug}', [ProductController::class, 'show'])
        ->name('products.show');
    // Dashboard toko (list produk milik seller)
        Route::get('/seller/products', [SellerProductController::class, 'index'])
            ->name('seller.products.index');

    /*
    |--------------------------------------------------------------------------
    | STORE REGISTRATION (member yang mau jadi seller)
    |--------------------------------------------------------------------------
    */

    // Halaman pendaftaran store
    Route::get('/store/registration', [StoreRegistrationController::class, 'create'])
        ->name('store.registration.create');
    Route::post('/store/registration', [StoreRegistrationController::class, 'store'])
        ->name('store.registration.store');

    // Buyer Profile Creation & Update
    Route::get('/buyer/profile/create', [BuyerProfileController::class, 'create'])
        ->name('buyer.profile.create');
    Route::post('/buyer/profile', [BuyerProfileController::class, 'store'])
        ->name('buyer.profile.store');
    
    // Update buyer profile (untuk yang sudah punya buyer profile)
    Route::patch('/buyer/profile', [BuyerProfileController::class, 'update'])
        ->name('buyer.profile.update');
    
    /*
    |--------------------------------------------------------------------------
    | BUYER AREA (harus punya Buyer profile) 
    | middleware tambahan: buyer.profile
    |--------------------------------------------------------------------------
    */

    Route::middleware('buyer.profile')->group(function () {
    // Riwayat transaksi buyer
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    Route::get('/transactions/{code}', [TransactionController::class, 'show'])
        ->name('transactions.show');

    // Pay transaction
    Route::post('/transactions/{transaction}/pay', [TransactionController::class, 'pay'])
        ->name('transactions.pay');

    // Checkout (1 produk)
    Route::get('/products/{product}/checkout', [CheckoutController::class, 'create'])
        ->name('checkout.create');

    Route::post('/products/{product}/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');
    });

    /*
    |--------------------------------------------------------------------------
    | SELLER AREA (harus punya store & terverifikasi)
    | middleware tambahan: seller.verified
    |--------------------------------------------------------------------------
    */

    Route::middleware('seller.verified')->group(function () {
        // Product Categories Management
        Route::get('/seller/categories', [ProductCategoryController::class, 'index'])
            ->name('seller.categories.index');

        Route::get('/seller/categories/create', [ProductCategoryController::class, 'create'])
            ->name('seller.categories.create');

        Route::post('/seller/categories', [ProductCategoryController::class, 'store'])
            ->name('seller.categories.store');

        Route::get('/seller/categories/{category}/edit', [ProductCategoryController::class, 'edit'])
            ->name('seller.categories.edit');

        Route::put('/seller/categories/{category}', [ProductCategoryController::class, 'update'])
            ->name('seller.categories.update');

        Route::delete('/seller/categories/{category}', [ProductCategoryController::class, 'destroy'])
            ->name('seller.categories.destroy');

        // Profil toko
        Route::get('/seller/store/profile', [StoreProfileController::class, 'edit'])
            ->name('store.profile.edit');

        Route::put('/seller/store/profile', [StoreProfileController::class, 'update'])
            ->name('store.profile.update');

        Route::delete('/seller/store/profile', [StoreProfileController::class, 'destroy'])
            ->name('store.profile.destroy');

        // Tambah produk
        Route::get('/seller/products/create', [SellerProductController::class, 'create'])
            ->name('seller.products.create');

        Route::post('/seller/products', [SellerProductController::class, 'store'])
            ->name('seller.products.store');

        // Edit produk
        Route::get('/seller/products/{product}/edit', [SellerProductController::class, 'edit'])
            ->name('seller.products.edit');

        Route::put('/seller/products/{product}', [SellerProductController::class, 'update'])
            ->name('seller.products.update');

        // Hapus produk
        Route::delete('/seller/products/{product}', [SellerProductController::class, 'destroy'])
            ->name('seller.products.destroy');

        // Kelola gambar produk
        Route::get('/seller/products/{product}/images', [SellerProductController::class, 'images'])
            ->name('seller.products.images');

        Route::post('/seller/products/{product}/images', [SellerProductController::class, 'storeImage'])
            ->name('seller.products.images.store');

        Route::delete('/seller/products/{product}/images/{image}', [SellerProductController::class, 'destroyImage'])
            ->name('seller.products.images.destroy');

        // Manajemen pesanan (order management)
        Route::get('/seller/orders', [SellerOrderController::class, 'index'])
            ->name('seller.orders.index');

        Route::get('/seller/orders/{transaction}', [SellerOrderController::class, 'show'])
            ->name('seller.orders.show');

        Route::patch('/seller/orders/{transaction}/status', [SellerOrderController::class, 'updateStatus'])
            ->name('seller.orders.updateStatus');

        Route::patch('/seller/orders/{transaction}/tracking', [SellerOrderController::class, 'updateTracking'])
            ->name('seller.orders.updateTracking');

        // Saldo toko (store balance)
        Route::get('/seller/balance', [StoreBalanceController::class, 'index'])
            ->name('store.balance.index');

        // Penarikan saldo (withdrawal)
        Route::get('/seller/withdrawals', [WithdrawalController::class, 'index'])
            ->name('seller.withdrawals.index');

        Route::post('/seller/withdrawals', [WithdrawalController::class, 'store'])
            ->name('seller.withdrawals.store');
    });
});


/*
|--------------------------------------------------------------------------
| ADMIN AREA (pakai role:admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    // Store Verification Page
    Route::get('/admin/stores/verifications', [StoreVerificationController::class, 'index'])
        ->name('admin.stores.verifications.index');

    Route::put('/admin/stores/{store}/verify', [StoreVerificationController::class, 'verify'])
        ->name('admin.stores.verifications.verify');

    Route::delete('/admin/stores/{store}/reject', [StoreVerificationController::class, 'reject'])
        ->name('admin.stores.verifications.reject');

    // User & Store Management Page
    Route::get('/admin/users-stores', [AdminManagementController::class, 'index'])
        ->name('admin.users-stores.index');

    Route::delete('/admin/users/{user}', [AdminManagementController::class, 'destroyUser'])
        ->name('admin.users.destroy');

    Route::delete('/admin/stores/{store}', [AdminManagementController::class, 'destroyStore'])
        ->name('admin.stores.destroy');
});