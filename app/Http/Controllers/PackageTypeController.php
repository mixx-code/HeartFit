<?php

namespace App\Http\Controllers;

use App\Models\packageType;
use App\Http\Requests\StorepackageTypeRequest;
use App\Http\Requests\UpdatepackageTypeRequest;
use Illuminate\Http\Request;

class PackageTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $perPage = (int) $request->input('per_page', 10);

        $packageTypes = PackageType::query()
            ->select("*")
            ->when($q, function ($qb) use ($q) {
                $qb->where('packageType', 'like', "%{$q}%");
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();


        return view('admin.packageType.packageType', [
            'packageTypes' => $packageTypes,
            'perPage'   => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.packageType.addPackageType');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'packageType' => 'required|string|max:50',
        ]);

        packageType::create([
            'packageType' => $request->packageType
        ]);

        return redirect()->route('admin.packageType')->with('success', 'Package type berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(packageType $packageType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(packageType $packageType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatepackageTypeRequest $request, packageType $packageType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(packageType $packageType)
    {
        //
    }
}
