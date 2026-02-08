<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use App\Services\MRGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'         => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users,email'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $generatedMr = null;
        
        DB::transaction(function () use ($request, &$generatedMr) {
            // 1) Create user account
            $user = User::create([
                'name'       => $request->username,
                'email'      => $request->email,
                'role'       => 'customer',
                'password'   => Hash::make($request->password),
                'created_by' => null, // System registration
            ]);

            // 2) Create user detail with auto-generated MR
            $generatedMr = MRGeneratorService::generateUnique();
            
            UserDetail::create([
                'user_id'    => $user->id,
                'mr'         => $generatedMr,
                'nik'        => 'TEMP-' . time(), // Temporary NIK, will be updated later
                'alamat'     => 'Belum diisi',
                'jenis_kelamin' => null,
                'tempat_lahir' => null,
                'tanggal_lahir' => null,
                'bb_tb'      => null,
                'hp'         => null,
                'usia'       => null,
                'created_by' => null,
            ]);
        });

        // Auto login after registration
        Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password
        ]);

        return redirect()
            ->route('dashboard.customer')
            ->with('status', "Registrasi berhasil! MR Anda: {$generatedMr}")
            ->with('info', 'Silakan lengkapi data profil Anda di menu akun untuk pengalaman yang lebih baik.');
    }
}
