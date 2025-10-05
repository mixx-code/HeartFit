<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $role = Auth::user()->role ?? 'customer';

            return $role === 'admin'
                ? redirect()->route('dashboard.admin')
                : redirect()->route('dashboard.customer');
        }

        // belum login → tampilkan landing page
        return view('welcome');
    }
}
