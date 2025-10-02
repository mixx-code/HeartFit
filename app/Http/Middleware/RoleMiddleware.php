<?php
// app/Http/Middleware/CheckRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = Auth::user();
        if (!$user || ($user->role ?? null) !== $role) {
            // kalau belum login, middleware 'auth' akan nge-handle di group; ini fallback aman
            return redirect()->route('login')->withErrors(['email' => 'Tidak punya akses.']);
        }
        return $next($request);
    }
}
