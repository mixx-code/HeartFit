<?php

namespace App\Http\Controllers;

use App\Models\MealPackages;
use App\Models\PackageType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MealPackagesController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));
        $perPage = (int) $request->input('per_page', 10);

        $packages = MealPackages::with('packageType')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('batch', 'like', "%{$q}%")
                        ->orWhere('jenis_paket', 'like', "%{$q}%")
                        ->orWhere('porsi_paket', 'like', "%{$q}%")
                        ->orWhere('detail_paket', 'like', "%{$q}%")
                        ->orWhereHas('packageType', function ($t) use ($q) {
                            $t->where('packageType', 'like', "%{$q}%");
                        });
                });
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.mealPackage.mealPackage', compact('packages', 'perPage'));
    }



    public function create()
    {
        $packageTypes = PackageType::all(); // ambil semua tipe paket
        return view('admin.mealPackage.addMealPackage', compact('packageTypes'));
    }


    public function store(Request $request)
    {
        $allowedJenis = ['paket 3 bulanan', 'paket bulanan', 'paket mingguan', 'harian'];
        $data = $request->validate([
            'nama_meal_package' => 'required|string|max:255',
            'batch'             => 'nullable|string',
            'jenis_paket'       => ['required', Rule::in($allowedJenis)],
            'porsi_paket'       => 'required|string',
            'total_hari'        => 'required|numeric|min:1',
            'detail_paket'      => 'required|string',
            'price'             => 'required|numeric|min:0',
            'package_type_id'   => 'required|exists:package_types,id',
        ]);


        MealPackages::create($data);

        return redirect()
            ->route('admin.mealPackage')
            ->with('status', 'Paket berhasil ditambahkan');
    }

    public function edit(MealPackages $mealPackage)
    {
        $packageTypes = PackageType::all();

        // Opsi porsi untuk dropdown (supaya konsisten dengan form create)
        $porsiOptions = [
            'harga per porsi',
            '4 hari 2 kali makan (siang dan sore)',
            '8 hari 1 kali makan (siang/malam saja)',
            '12 hari 2 kali makan (siang dan sore)',
            '24 hari 1 kali makan (siang/malam saja)',
            '36 hari 2 kali makan (siang dan sore)',
            '72 hari 1 kali makan (siang/malam saja)',
            '2 kali makan (siang dan malam)',
        ];

        return view('admin.mealPackage.editMealPackage', compact('mealPackage', 'packageTypes', 'porsiOptions'));
    }

    /** Update data */
    public function update(Request $request, MealPackages $mealPackage)
    {
        $allowedJenis = ['paket 3 bulanan', 'paket bulanan', 'paket mingguan', 'harian'];
        $data = $request->validate([
            'nama_meal_package' => 'required|string|max:255',
            'batch'             => 'nullable|string',
            'jenis_paket'       => ['required', Rule::in($allowedJenis)],
            'porsi_paket'       => 'required|string',
            'total_hari'        => 'required|numeric|min:1',
            'detail_paket'      => 'required|string',
            'price'             => 'required|numeric|min:0',
            'package_type_id'   => 'required|exists:package_types,id',
        ]);


        $mealPackage->update($data);

        // sesuaikan dengan nama route index kamu
        return redirect()->route('admin.mealPackage')->with('status', 'Paket berhasil diperbarui');
    }

    /** Soft delete */
    public function destroy(MealPackages $mealPackage)
    {
        $mealPackage->delete(); // ← soft delete (isi deleted_at)

        return redirect()->route('admin.mealPackage')->with('status', 'Paket berhasil dihapus');
    }
}
