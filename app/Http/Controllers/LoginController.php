<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $users = [
        [
            'username' => 'superadmin',
            'email'    => 'superadmin@mail.com',
            'password' => 'superadmin123',
            'role'     => 'admin',
        ],
        [
            'username' => 'customer',
            'email'    => 'customer@mail.com',
            'password' => 'customer123',
            'role'     => 'customer',
        ],
    ];

    public function showLoginForm()
    {
        return view('auth.login'); // file blade kamu
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $emailOrUsername = $request->input('email'); // bisa email atau username
        $password        = $request->input('password');

        // cek di dummy users
        $user = collect($this->users)->first(function ($u) use ($emailOrUsername, $password) {
            return (
                ($u['email'] === $emailOrUsername || $u['username'] === $emailOrUsername)
                && $u['password'] === $password
            );
        });

        if (!$user) {
            return back()->withErrors(['email' => 'Email/Username atau password salah.'])->withInput();
        }

        // Simpan ke session
        session([
            'user' => [
                'username' => $user['username'],
                'email'    => $user['email'],
                'role'     => $user['role'],
            ],
        ]);

        // Redirect berdasarkan role
        if ($user['role'] === 'admin') {
            return redirect()->route('dashboard.admin')->with('status', 'Selamat datang Admin!');
        }

        if ($user['role'] === 'customer') {
            return redirect()->route('dashboard.customer')->with('status', 'Selamat datang Customer!');
        }

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->route('welcome')->with('toast_success', 'Berhasil logout. Sampai jumpa!');;
    }
}
