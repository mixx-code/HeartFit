<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MealPackagesController;
use App\Http\Controllers\MenuMakananController;
use App\Http\Controllers\PackageTypeController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDetailController;

Route::get('/', [LandingPageController::class, 'index'])->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::view('/registrasi', 'auth.register')->name('registrasi');
});

Route::middleware(['auth', 'session.timeout'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::view('/dashboard/admin', 'admin.dashboard')->name('dashboard.admin');

        // Route::resource('petugas', PetugasController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        Route::view('/admin/products/add', 'admin.products.addPaketMakanan')->name('admin.products.add');

        // === ROUTE DATA PETUGAS/ADMIN ===
        Route::get('/admin/data/petugas', [PetugasController::class, 'index'])->name('admin.data.petugas');

        Route::get('/admin/data/petugas/create', [PetugasController::class, 'create'])->name('admin.data.petugas.create');

        Route::post('/admin/data/petugas/create', [PetugasController::class, 'store'])->name('admin.data.petugas.store');

        Route::get('/admin/data/petugas/detail/{user_detail}', [PetugasController::class, 'show'])->name('admin.data.petugas.detail');

        Route::put('/admin/data/petugas/detail/{user_detail}', [PetugasController::class, 'update'])->name('admin.data.petugas.update');

        Route::delete('/admin/data/petugas/{user}', [PetugasController::class, 'destroy'])->name('admin.data.petugas.delete');


        // route data customers
        Route::get('/admin/data/customers', [CustomerController::class, 'index'])->name('admin.data.customers');

        Route::get('/admin/data/customers/create', [CustomerController::class, 'create'])->name('admin.data.customers.create');

        Route::post('/admin/data/customers/create', [UserDetailController::class, 'store'])->name('admin.data.customers.create');

        Route::get('/admin/data/customer/detail/{user_detail}', [UserDetailController::class, 'show'])->name('admin.data.customer.detail');

        Route::put('/admin/data/customer/detail/{user_detail}', [UserDetailController::class, 'update'])->name('admin.user-details.update');

        Route::delete('/admin/data/customer/{user}', [UserController::class, 'destroy'])
            ->name('admin.data.customer.delete');

        // akhir data customers

        // route package type

        Route::get('/admin/packageType/addPackageType', [PackageTypeController::class, 'create'])->name('admin.packageType.addPackageType');

        Route::post('/admin/packageType/store', [PackageTypeController::class, 'store'])->name('admin.packageType.store');

        Route::get('/admin/packageType', [PackageTypeController::class, 'index'])->name('admin.packageType');
        
        // akhir route package type

        // route meal package

        Route::get('/admin/mealPackage/addMealPackage', [MealPackagesController::class, 'create'])->name('admin.mealPackage.addMealPackage');

        Route::post('/admin/mealPackage/store', [MealPackagesController::class, 'store'])->name('admin.mealPackage.store');

        Route::get('/admin/mealPackage', [MealPackagesController::class, 'index'])->name('admin.mealPackage');

        Route::get('/admin/mealPackage/edit/{mealPackage}', [MealPackagesController::class, 'edit'])->name('admin.mealPackage.edit');

        Route::put('/admin/mealPackage/edit/{mealPackage}', [MealPackagesController::class, 'update'])->name('admin.mealPackage.update');

        Route::delete('/admin/mealPackage/delete/{mealPackage}', [MealPackagesController::class, 'destroy'])
            ->name('admin.mealPackage.delete');
        
        // akhir route meal package

        // route menu makanan

        Route::get('/admin/menuMakanan', [MenuMakananController::class, 'index'])->name('admin.menuMakanan');
        Route::get('/admin/menuMakanan/addMenuMakanan', [MenuMakananController::class, 'create'])->name('admin.menuMakanan.addMenuMakanan');

        Route::post('/admin/menuMakanan/store', [MenuMakananController::class, 'store'])->name('admin.menuMakanan.store');

        Route::get('/admin/menuMakanan/edit/{menuMakanan}', [MenuMakananController::class, 'edit'])->name('admin.menuMakanan.edit');

        Route::put('/admin/menuMakanan/edit/{menuMakanan}', [MenuMakananController::class, 'update'])->name('admin.menuMakanan.update');

        Route::delete('/admin/menuMakanan/delete/{menuMakanan}', [MenuMakananController::class, 'destroy'])
            ->name('admin.menuMakanan.delete');
        
        // akhir route menu makanan

        // route products
        // Route::get('/admin/products/packageType', PackageTypeController::class, 'index')->name('admin.products.packageType');
        // akhir route products

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

        Route::get('/customer/orders', [OrderController::class, 'index'])->name('orders.index');

        Route::post('/orders',               [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}/pay',    [OrderController::class, 'pay'])->name('orders.pay');
        Route::get('/orders/{order}/finish', [OrderController::class, 'finish'])->name('orders.finish');
        Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
        Route::get('/orders/{order}/status', [OrderController::class, 'statusJson'])->name('orders.status');
        Route::post('/orders/{order}/snap-result', [OrderController::class, 'snapResult'])
            ->name('orders.snap_result');
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
    });
});

