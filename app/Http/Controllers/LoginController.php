<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string', // bisa email ATAU username (pakai kolom name)
            'password' => 'required|string',
        ]);

        $login = $request->input('email');

        // Bisa login pakai email ATAU name (username)
        $credentials = [
            str_contains($login, '@') ? 'email' : 'name' => $login,
            'password' => $request->input('password'),
        ];

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Email/Username atau password salah.'])->withInput();
        }

        $request->session()->regenerate();
        $request->session()->put('login_time', now()->toISOString());
        session(['login_time_ts' => now()->timestamp]);

        // Arahkan ke dashboard sesuai role
        $role = Auth::user()->role ?? 'customer';
        $adminRoles = ['admin', 'superadmin', 'ahli_gizi', 'bendahara', 'medical_record', 'kurir'];
        return in_array($role, $adminRoles)
            ? redirect()->route('dashboard.admin')->with('status', 'Selamat datang!')
            : redirect()->route('dashboard.customer')->with('status', 'Selamat datang!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome')->with('toast_success', 'Berhasil logout. Sampai jumpa!');
    }
}
