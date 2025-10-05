<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDetailController;

Route::get('/', [LandingPageController::class, 'index'])->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::middleware(['auth', 'session.timeout'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::view('/dashboard/admin', 'admin.dashboard')->name('dashboard.admin');
        Route::resource('petugas', PetugasController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::view('/admin/products/add', 'admin.products.addPaketMakanan')->name('admin.products.add');

        // route data customers
        Route::get('/admin/data/customers', [CustomerController::class, 'index'])->name('admin.data.customers');

        Route::get('/admin/data/customers/create', [CustomerController::class, 'create'])->name('admin.data.customers.create');

        Route::post('/admin/data/customers/create', [UserDetailController::class, 'store'])->name('admin.data.customers.create');

        Route::get('/admin/data/customer/detail/{user_detail}', [UserDetailController::class, 'show'])->name('admin.data.customer.detail');

        // akhir data customers
        Route::resource('user-details', UserDetailController::class);


        // Route::get('/admin/data/customers', [UserController::class, 'customers'])
        //     ->name('admin.customers');
            
        // Route::view('/admin/data/customers/create', 'admin.customers.customers-create')->name('admin.customers.create');

        Route::resource('admin', UserController::class)->only(['index', 'customers', 'create', 'store', 'edit', 'update', 'destroy', ]);

    });

    Route::middleware('role:customer')->group(function () {
        Route::view('/dashboard/customer', 'customers.dashboard')->name('dashboard.customer');

        
        Route::get('/customers/create',    [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers',          [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{id}',      [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{id}',   [CustomerController::class, 'destroy'])->name('customers.destroy');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

        Route::post('/orders',               [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}/pay',    [OrderController::class, 'pay'])->name('orders.pay');
        Route::get('/orders/{order}/finish', [OrderController::class, 'finish'])->name('orders.finish');
        Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
        Route::get('/orders/{order}/status', [OrderController::class, 'statusJson'])->name('orders.status');
        Route::post('/orders/{order}/snap-result', [OrderController::class, 'snapResult'])
            ->name('orders.snap_result');
    });
});

// Public steps
Route::get('/orders/create',   [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders/preview', [OrderController::class, 'preview'])->name('orders.preview');

// Webhook (jangan lupa exclude CSRF di VerifyCsrfToken)
Route::post('/midtrans/webhook', [OrderController::class, 'webhook'])->name('midtrans.webhook');
Route::get('/whoami', function () {
    return [
        'auth' => Auth::check(),
        'id'   => Auth::id(),
        'user' => Auth::user(),
        'session_id' => session()->getId(),
    ];
})->middleware('web');
