<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = session('user');

        // Jika belum login
        if (!$user) {
            return redirect()->route('login')->with('status', 'Kamu harus login terlebih dahulu.');
        }

        // Jika role tidak sesuai
        if ($user['role'] !== $role) {
            return redirect()->route('login')->with('status', 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
