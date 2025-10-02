<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $roles)
    {
        // asumsikan route diletakkan di dalam group 'auth'
        $user = $request->user(); // sama dengan Auth::user()

        // Jika entah bagaimana belum login, serahkan ke middleware 'auth' di luar
        if (!$user) {
            return $next($request); // biar 'auth' yang handle. Atau return redirect()->route('login');
        }

        // Dukung banyak role: role1,role2
        $allowed = collect(explode(',', $roles))->map(fn($r) => trim($r))->filter()->all();

        if (!in_array($user->role ?? '', $allowed, true)) {
            // BUKAN redirect ke /login (itu bikin loop)
            // Pilihan 1: Forbidden
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');

            // Pilihan 2 (alternatif): redirect ke dashboard sesuai role sebenarnya
            // return redirect()->route($user->role === 'admin' ? 'dashboard.admin' : 'dashboard.customer')
            //        ->with('toast_error', 'Anda tidak memiliki akses ke halaman itu.');
        }

        return $next($request);
    }
}
