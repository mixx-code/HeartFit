<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PetugasController extends Controller
{
    /** LIST + SEARCH + PAGINATION */
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q'));
        $perPage = (int) $request->input('per_page', 10);

        $petugas = User::query()
            ->where('role', '!=', 'customer')
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($b) use ($q) {
                    $b->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.petugas.petugas', [
            'petugas' => $petugas,
            'perPage'   => $perPage,
        ]);
    }

    /** FORM CREATE */
    public function create(Request $request)
    {
        return view('admin.petugas.petugas-create');
    }

    /** STORE (simpan data baru) */
    public function store(Request $request)
    {
        // Validasi form sesuai input di Blade
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'role'          => 'required|string',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'       => 'required|string|min:6|confirmed',
            'hp'            => 'nullable|string|max:25',
        ]);

        // Buat akun user dengan password dari form
        $user = User::create([
            'name'       => $request->nama,
            'role'       => $request->role,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'created_by' => Auth::id(),
        ]);

        // Untuk petugas/admin tidak perlu user_details (karena tidak ada MR)
        return redirect()
            ->route('admin.data.petugas')
            ->with('success', 'Akun petugas berhasil dibuat!');
    }

    public function show(User $user)
    {
        // Untuk petugas, kita tidak punya user_details, jadi tampilkan data user saja
        return view('admin.petugas.petugas-detail-new', [
            'user' => $user,
        ]);
    }

    /** FORM EDIT */
    public function edit(User $user)
    {
        // Untuk petugas, tampilkan form edit sederhana
        return view('admin.petugas.petugas-edit', [
            'user' => $user,
        ]);
    }

    /** UPDATE DATA */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'  => 'required|string|in:admin,ahli_gizi',
        ]);

        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'updated_by' => Auth::id(),
            ]);

            return redirect()
                ->route('admin.data.petugas')
                ->with('success', 'Data petugas berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Petugas berhasil dihapus.');
    }

    /** CREATE ADMIN - Superadmin only */
    public function createAdmin()
    {
        return view('admin.petugas.create-admin');
    }

    /** STORE ADMIN - Superadmin only */
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'       => 'required|string|min:6|confirmed',
            'hp'            => 'nullable|string|max:25',
        ]);

        // Buat akun admin dengan role 'admin'
        $user = User::create([
            'name'       => $validated['name'],
            'role'       => 'admin', // Force role to admin
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.data.petugas')
            ->with('success', 'Akun admin berhasil dibuat!');
    }
}
