<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function customers(Request $request)
    {
        $q       = trim((string) $request->input('q'));
        $perPage = (int) $request->input('per_page', 10);

        $customers = User::query()
            ->where('role', 'customer')
            ->when($q, function ($qBuilder) use ($q) {
                $qBuilder->where(function ($b) use ($q) {
                    $b->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString(); // penting untuk mempertahankan q & per_page

        return view('admin.customers.customers', [
            'customers' => $customers,
            'perPage'   => $perPage,
        ]);
    }

    public function create(Request $request)
    {
        $q       = trim((string) $request->input('q'));
        $perPage = (int) $request->input('per_page', 10);

        $customers = User::query()
            ->where('role', 'customer')
            ->when($q, function ($qBuilder) use ($q) {
                $qBuilder->where(function ($b) use ($q) {
                    $b->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString(); // penting untuk mempertahankan q & per_page

        return view('admin.customers.customers', [
            'customers' => $customers,
            'perPage'   => $perPage,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'role'     => 'required|string',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'       => $request->name,
            'role'       => $request->role,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'role'     => 'required|string',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->update([
            'name'       => $request->name,
            'role'       => $request->role,
            'email'      => $request->email,
            'password'   => $request->password ? Hash::make($request->password) : $user->password,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        $user->update([
            'deleted_by' => Auth::id(),
        ]);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}
