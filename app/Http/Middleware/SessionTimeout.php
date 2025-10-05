<?php
// app/Http/Middleware/SessionTimeout.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $loginTs = (int) session('login_time_ts', 0);

            // kalau belum ada (misal langsung akses route setelah login), set sekarang
            if ($loginTs === 0) {
                session(['login_time_ts' => now()->timestamp]);
            } else {
                // ABSOLUTE TIMEOUT: tendang kalau sudah >= 60 detik dari waktu login
                if ((now()->timestamp - $loginTs) >= 60*60) { // 60 = 1 menit; ganti 300 untuk 5 menit
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Session expired, silakan login lagi.'
                    ]);
                }
            }

            // >>> Jika mau “idle timeout” (reset tiap request), UNCOMMENT baris di bawah:
            // session(['login_time_ts' => now()->timestamp]);
        }

        return $next($request);
    }
}
