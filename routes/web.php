<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardCustomerController;
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
    Route::get('/debug-role', function () {
        $u = Auth::user();
        return [
            'raw'   => $u->role ?? null,
            'norm'  => strtolower(str_replace([' ', '-'], '_', trim($u->role ?? ''))),
            'id'    => $u->id ?? null,
        ];
    })->middleware('auth');

    Route::get('/debug-session', function () {
        $user = Auth::user();

        return [
            'user' => [
                'id'       => $user->id ?? null,
                'name'     => $user->name ?? null,
                'email'    => $user->email ?? null,
                'role_raw' => $user->role ?? null,
                'role_norm' => strtolower(str_replace([' ', '-'], '_', trim($user->role ?? ''))),
            ],
            'session' => [
                'session_id'     => session()->getId(),
                'login_time'     => session('login_time'),
                'login_time_ts'  => session('login_time_ts'),
                'all_session'    => session()->all(), // tampilkan semua session
            ],
            'auth' => [
                'is_authenticated' => Auth::check(),
                'guard'            => config('auth.defaults.guard'),
            ],
        ];
    })->middleware('auth');

    Route::get('/debug-order-window', function () {
        $tz = config('app.timezone', 'Asia/Jakarta');

        $today    = now($tz)->toDateString();
        $tomorrow = now($tz)->addDay()->toDateString();
        $userId   = Auth::id();

        $countUser = \App\Models\Order::where('user_id', $userId)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $tomorrow)
            ->count();

        $countGlobal = \App\Models\Order::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $tomorrow)
            ->count();

        return [
            'tz'            => $tz,
            'today'         => $today,
            'tomorrow'      => $tomorrow,
            'count_user'    => $countUser,
            'count_global'  => $countGlobal,
            'user_id'       => $userId,
        ];
    })->middleware('auth');




    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // =======================
    // ADMIN DASHBOARD — akses: admin | ahli_gizi | bendahara | medical_record
    // =======================
    Route::middleware('role:admin,ahli_gizi,bendahara,medical_record')->group(function () {
        Route::get('/dashboard/admin', [DashboardAdminController::class, 'index'])->name('dashboard.admin');
    });

    // =======================
    // ADMIN ONLY
    // =======================
    Route::middleware('role:admin')->group(function () {


        Route::patch('/admin/deliveries/{delivery}/update-status', [DashboardAdminController::class, 'updateStatus'])
            ->name('admin.deliveries.updateStatus');

        Route::view('/admin/products/add', 'admin.products.addPaketMakanan')
            ->name('admin.products.add');

        // Data Petugas/Admin
        Route::get('/admin/data/petugas', [PetugasController::class, 'index'])->name('admin.data.petugas');
        Route::get('/admin/data/petugas/create', [PetugasController::class, 'create'])->name('admin.data.petugas.create');
        Route::post('/admin/data/petugas/create', [PetugasController::class, 'store'])->name('admin.data.petugas.store');
        Route::get('/admin/data/petugas/detail/{user_detail}', [PetugasController::class, 'show'])->name('admin.data.petugas.detail');
        Route::put('/admin/data/petugas/detail/{user_detail}', [PetugasController::class, 'update'])->name('admin.data.petugas.update');
        Route::delete('/admin/data/petugas/{user}', [PetugasController::class, 'destroy'])->name('admin.data.petugas.delete');

        // Package Type
        Route::get('/admin/packageType', [PackageTypeController::class, 'index'])->name('admin.packageType');
        Route::get('/admin/packageType/addPackageType', [PackageTypeController::class, 'create'])->name('admin.packageType.addPackageType');
        Route::post('/admin/packageType/store', [PackageTypeController::class, 'store'])->name('admin.packageType.store');
        Route::get('/admin/packageType/edit/{packageType}', [PackageTypeController::class, 'edit'])->name('admin.packageType.edit');
        Route::put('/admin/packageType/update/{packageType}', [PackageTypeController::class, 'update'])->name('admin.packageType.update');
        Route::delete('/admin/packageType/delete/{packageType}', [PackageTypeController::class, 'destroy'])->name('admin.packageType.destroy');

        // Meal Package
        Route::get('/admin/mealPackage/addMealPackage', [MealPackagesController::class, 'create'])->name('admin.mealPackage.addMealPackage');
        Route::post('/admin/mealPackage/store', [MealPackagesController::class, 'store'])->name('admin.mealPackage.store');
        Route::get('/admin/mealPackage', [MealPackagesController::class, 'index'])->name('admin.mealPackage');
        Route::get('/admin/mealPackage/edit/{mealPackage}', [MealPackagesController::class, 'edit'])->name('admin.mealPackage.edit');
        Route::put('/admin/mealPackage/edit/{mealPackage}', [MealPackagesController::class, 'update'])->name('admin.mealPackage.update');
        Route::delete('/admin/mealPackage/delete/{mealPackage}', [MealPackagesController::class, 'destroy'])->name('admin.mealPackage.delete');

        // Resource admin (pastikan nama2 method yang dipakai unik & gak bentrok)
        Route::resource('admin', UserController::class)
            ->only(['index', 'customers', 'create', 'store', 'edit', 'update', 'destroy']);
    });

    // =======================
    // CUSTOMERS MANAGEMENT — akses: admin + medical_record
    // (SATU DEFINISI ROUTE SAJA)
    // =======================
    Route::middleware('role:admin,medical_record')->group(function () {
        Route::get('/admin/data/customers', [CustomerController::class, 'index'])->name('admin.data.customers');
        Route::get('/admin/data/customers/create', [CustomerController::class, 'create'])->name('admin.data.customers.create');
        Route::post('/admin/data/customers/create', [UserDetailController::class, 'store'])->name('admin.data.customers.create');
        Route::get('/admin/data/customer/detail/{user_detail}', [UserDetailController::class, 'show'])->name('admin.data.customer.detail');
        Route::put('/admin/data/customer/detail/{user_detail}', [UserDetailController::class, 'update'])->name('admin.user-details.update');
        Route::delete('/admin/data/customer/{user}', [UserController::class, 'destroy'])->name('admin.data.customer.delete');
    });

    // =======================
    // ORDERS LIST — akses: admin + bendahara
    // (SATU DEFINISI ROUTE SAJA)
    // =======================
    Route::middleware('role:admin,bendahara')->group(function () {
        // Tetap satu nama: admin.orders.index (biar menu kamu konsisten)
        Route::get('/admin/orders', [OrderController::class, 'viewOrderByAdmin'])->name('admin.orders.index');
        // Kalau kamu mau URL khusus bendahara (mis. /bendahara/orders), beri NAMA BERBEDA
        // Route::get('/bendahara/orders', [OrderController::class, 'viewOrderByAdmin'])->name('bendahara.orders.index');
    });

    // =======================
    // MENU MAKANAN — akses: admin + ahli_gizi
    // (SATU DEFINISI ROUTE SAJA)
    // =======================
    Route::middleware('role:admin,ahli_gizi')->group(function () {
        // Nama route tetap "admin.menuMakanan*" supaya sidebar kamu gak perlu diubah
        Route::get('/admin/menuMakanan', [MenuMakananController::class, 'index'])->name('admin.menuMakanan');
        Route::get('/admin/menuMakanan/addMenuMakanan', [MenuMakananController::class, 'create'])->name('admin.menuMakanan.addMenuMakanan');
        Route::post('/admin/menuMakanan/store', [MenuMakananController::class, 'store'])->name('admin.menuMakanan.store');
        Route::get('/admin/menuMakanan/edit/{menuMakanan}', [MenuMakananController::class, 'edit'])->name('admin.menuMakanan.edit');
        Route::put('/admin/menuMakanan/edit/{menuMakanan}', [MenuMakananController::class, 'update'])->name('admin.menuMakanan.update');
        Route::delete('/admin/menuMakanan/delete/{menuMakanan}', [MenuMakananController::class, 'destroy'])->name('admin.menuMakanan.delete');
    });

    Route::middleware('role:customer')->group(function () {
        // Route::view('/dashboard/customer', 'customers.dashboard')->name('dashboard.customer');
        Route::get('/dashboard/customer', [DashboardCustomerController::class, 'index'])
            ->name('dashboard.customer');

        
        Route::get('/customers/create',    [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers',          [CustomerController::class, 'store'])->name('customers.store');

        Route::get('/customer/akun/detail/{user_detail}', [UserDetailController::class, 'showAkun'])->name('customer.data.customer.detail');

        Route::put('/customer/data/akun/detail/{user_detail}', [UserDetailController::class, 'updateAkun'])->name('customer.akun.update');

        Route::get('/customer/orders', [OrderController::class, 'index'])->name('customer.orders.index');

        Route::post('/customer/orders',               [OrderController::class, 'store'])->name('orders.store')->middleware('block.order.window.db');
        Route::get('/customer/orders/{order}/pay',    [OrderController::class, 'pay'])->name('orders.pay');
        Route::get('/customer/orders/{order}/finish', [OrderController::class, 'finish'])->name('orders.finish');
        Route::post('/customer/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
        Route::get('/customer/orders/{order}/status', [OrderController::class, 'statusJson'])->name('orders.status');
        Route::post('/customer/orders/{order}/snap-result', [OrderController::class, 'snapResult'])
            ->name('orders.snap_result');
            // Public steps
        Route::get('/customer/orders/create',   [OrderController::class, 'create'])->name('orders.create')->middleware('block.order.window.db');
        Route::post('/customer/orders/preview', [OrderController::class, 'preview'])->name('orders.preview');
            
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

