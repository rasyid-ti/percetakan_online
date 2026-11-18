<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Operator\TaskController as OperatorTaskController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ReportController as OwnerReportController;

// Halaman publik
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/katalog', [PageController::class, 'catalog'])->name('catalog');
Route::get('/produk/{product:slug}', [PageController::class, 'product'])->name('product.show');

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login']);
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);
});

Route::post('/keluar', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Pelanggan
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::prefix('pelanggan')->name('customer.')->group(function () {
        Route::view('/dashboard', 'customer.dashboard')->name('dashboard');
    });

    Route::prefix('keranjang')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/tambah/{product:slug}', [CartController::class, 'store'])->name('store');
        Route::delete('/{key}', [CartController::class, 'destroy'])->name('destroy');
    });

    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::prefix('pesanan')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order:code}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order:code}/bayar', [OrderController::class, 'pay'])->name('pay');
        Route::post('/{order:code}/batal', [OrderController::class, 'cancel'])->name('cancel');
        Route::get('/{order:code}/bukti', [OrderController::class, 'proof'])->name('proof');
        Route::get('/{order:code}/desain/{item}', [OrderController::class, 'design'])->name('design');
    });
});

// Admin
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index')->defaults('mode', 'orders');
        Route::get('/pembayaran', [AdminOrderController::class, 'index'])->name('payments')->defaults('mode', 'payments');

        Route::get('/pesanan/{order:code}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/pesanan/{order:code}/pembayaran/{payment}/setujui', [AdminOrderController::class, 'approve'])->name('orders.approve');
        Route::post('/pesanan/{order:code}/pembayaran/{payment}/tolak', [AdminOrderController::class, 'reject'])->name('orders.reject');
        Route::post('/pesanan/{order:code}/operator', [AdminOrderController::class, 'assign'])->name('orders.assign');
        Route::post('/pesanan/{order:code}/selesai', [AdminOrderController::class, 'complete'])->name('orders.complete');
        Route::post('/pesanan/{order:code}/batal', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/pesanan/{order:code}/desain/{item}', [AdminOrderController::class, 'design'])->name('orders.design');
        Route::get('/pembayaran/{payment}/bukti', [AdminOrderController::class, 'proof'])->name('payments.proof');

        Route::resource('produk', AdminProductController::class)
            ->parameters(['produk' => 'product'])
            ->names('products')
            ->except('show');
        Route::post('/produk/{product}/aktif', [AdminProductController::class, 'toggle'])->name('products.toggle');

        Route::resource('kategori', AdminCategoryController::class)
            ->parameters(['kategori' => 'category'])
            ->names('categories')
            ->only(['index', 'store', 'edit', 'update', 'destroy']);

        Route::resource('pengguna', AdminUserController::class)
            ->parameters(['pengguna' => 'user'])
            ->names('users')
            ->only(['index', 'create', 'store', 'edit', 'update']);
    });

// Operator
Route::middleware(['auth', 'role:operator'])
    ->prefix('operator')->name('operator.')
    ->group(function () {
        Route::get('/dashboard', [OperatorTaskController::class, 'index'])->name('dashboard');
        Route::get('/riwayat', [OperatorTaskController::class, 'history'])->name('history');
        Route::post('/pesanan/{order:code}/maju', [OperatorTaskController::class, 'advance'])->name('advance');
        Route::get('/pesanan/{order:code}/desain/{item}', [OperatorTaskController::class, 'design'])->name('design');
        Route::resource('produk', AdminProductController::class)
            ->parameters(['produk' => 'product'])
            ->names('products')
            ->except('show');
        Route::post('/produk/{product}/aktif', [AdminProductController::class, 'toggle'])->name('products.toggle');

        Route::resource('kategori', AdminCategoryController::class)
            ->parameters(['kategori' => 'category'])
            ->names('categories')
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
    });

// Owner
Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')->name('owner.')
    ->group(function () {
        Route::get('/dashboard', OwnerDashboardController::class)->name('dashboard');
        Route::get('/laporan', [OwnerReportController::class, 'index'])->name('report');
        Route::get('/laporan/unduh', [OwnerReportController::class, 'export'])->name('report.export');
    });