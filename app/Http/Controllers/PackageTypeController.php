<?php

namespace App\Http\Controllers;

use App\Models\PackageType; // pastikan ini PascalCase
use App\Http\Requests\UpdatepackageTypeRequest;
use Illuminate\Http\Request;

class PackageTypeController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $perPage = (int) $request->input('per_page', 10);

        $packageTypes = PackageType::query()
            ->when($q, fn($qb) => $qb->where('packageType', 'like', "%{$q}%"))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.packageType.packageType', [
            'packageTypes' => $packageTypes,
            'perPage'      => $perPage,
        ]);
    }

    public function create()
    {
        return view('admin.packageType.addPackageType');
    }

    public function store(Request $request)
    {
        $request->validate([
            'packageType' => 'required|string|max:50',
        ]);

        PackageType::create([
            'packageType' => $request->packageType,
        ]);

        return redirect()
            ->route('admin.packageType')
            ->with('success', 'Package type berhasil ditambahkan');
    }

    /**
     * EDIT: Ambil data by id lalu kirim ke view edit
     */
    public function edit(PackageType $packageType)
    {
        return view('admin.packageType.editPackageType', [
            'packageType' => $packageType,
        ]);
    }

    /**
     * UPDATE: Simpan perubahan dari form edit
     */
    public function update(Request $request, PackageType $packageType)
    {
        $data = $request->validate([
            'packageType' => 'required|string|max:50',
        ]);

        $packageType->update($data);
        return redirect()
            ->route('admin.packageType')
            ->with('success', 'Package type berhasil diperbarui');
    }

    /**
     * DESTROY: Hapus by id
     */
    public function destroy(PackageType $packageType)
    {
        $packageType->delete();

        return redirect()
            ->route('admin.packageType')
            ->with('success', 'Package type berhasil dihapus');
    }
}
