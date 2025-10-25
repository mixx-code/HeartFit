<?php

use App\Http\Middleware\BlockOrderWindowFromDB;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// ← kalau kamu punya middleware Role sendiri, pastikan namespace/namanya benar:
use App\Http\Middleware\CheckRole;   // atau RoleMiddleware kalau itu nama file-mu

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias route middleware (ganti Kernel::routeMiddleware)
        $middleware->alias([
            // Pakai middleware bawaan Laravel:
            'auth'  => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,

            // (opsional bawaan lain kalau perlu)
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            // Middleware role buatanmu:
            'role' => CheckRole::class,   // pastikan class-nya ada & benar
            'session.timeout' => \App\Http\Middleware\SessionTimeout::class,
            'block.order.window.db' => BlockOrderWindowFromDB::class,
        ]);

        // Kalau sebelumnya kamu bikin 'auth.session' custom, HAPUS aja alias itu.
        // Laravel sudah menyediakan 'auth' bawaan yang bekerja dengan Auth::attempt().
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
