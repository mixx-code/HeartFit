<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    private function normalize(string $role): string
    {
        $r = strtolower(trim($role));
        $r = str_replace([' ', '-'], '_', $r);
        return preg_replace('/[^a-z0-9_]/', '', $r) ?? '';
    }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('web')->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Dukung "role:a,b,c" atau "role:a|b|c"
        $allowed = collect($roles)
            ->flatMap(fn($r) => preg_split('/[|,]/', (string) $r))
            ->map(fn($r) => $this->normalize($r))
            ->filter()
            ->values()
            ->all();

        $userRole = $this->normalize((string) ($user->role ?? ''));

        if (!in_array($userRole, $allowed, true)) {
            // Debug sementara (boleh diaktifkan kalau perlu):
            // \Log::warning('Role denied', compact('userRole','allowed'));
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
