<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PetugasController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [LandingPageController::class, 'index'])->name('welcome');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// });
Route::get('/addPaketMakanan', function () {
    return view('admin.products.addPaketMakanan');
});

// route customers
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');

Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

// route petugas
Route::resource('petugas', PetugasController::class)
->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
->names([
    'index'   => 'petugas.index',
    'create'  => 'petugas.create',
        'store'   => 'petugas.store',
        'edit'    => 'petugas.edit',
        'update'  => 'petugas.update',
        'destroy' => 'petugas.destroy',
    ]);
    
    Route::get('/addCustomer', function () {
        return view('addCustomer');
    });
    
    Route::get('/register', function () {
        return view('auth.register');
    });
    
    // halaman login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    
    // proses login
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    
    // logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard/admin', function () {
    return view('admin.dashboard');
})->name('dashboard.admin')->middleware('role:admin');

Route::get('/dashboard/customer', function () {
    return view('customers.dashboard');
})->name('dashboard.customer')->middleware('role:customer');

Route::get('/kalender', function () {
    return view('kalender');
});
