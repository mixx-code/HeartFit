<?php

namespace App\Http\Controllers;

use App\Models\MealPackages;
use Illuminate\Http\Request;

class MealPackagesController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $perPage = (int) $request->input('per_page', 10);

        $packages = MealPackages::with('packageType')
            ->when(
                $q,
                fn($qb) =>
                $qb->where('batch', 'like', "%{$q}%")
                    ->orWhere('jenis_paket', 'like', "%{$q}%")
                    ->orWhereHas('packageType', fn($t) => $t->where('packageType', 'like', "%{$q}%"))
            )
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.products.mealPackages', compact('packages', 'perPage'));
    }


    public function create()
    {
        return view('admin.meal-packages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'batch'          => 'nullable|string',
            'jenis_paket'    => 'required|in:harian,mingguan,bulanan',
            'porsi_paket'    => 'required|string',
            'detail_paket'   => 'required|string',
            'package_type_id' => 'required|exists:package_types,id',
        ]);

        MealPackages::create($data);

        return redirect()->route('meal-packages.index')->with('status', 'Paket berhasil ditambahkan');
    }
}
