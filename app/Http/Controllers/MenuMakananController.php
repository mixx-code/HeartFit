<?php

namespace App\Http\Controllers;

use App\Models\menuMakanan;
use Illuminate\Support\Arr;
use App\Http\Requests\UpdatemenuMakananRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuMakananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil parameter pencarian dan ukuran halaman dari request
        $search   = $request->input('q');
        $perPage  = (int) $request->input('per_page', 10); // default 10 baris per halaman

        // Query dasar
        $query = menuMakanan::query();

        // Jika ada pencarian, filter berdasarkan nama_menu atau batch
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                    ->orWhere('batch', 'like', "%{$search}%");
            });
        }

        // Urutkan dari yang terbaru
        $menus = $query->orderByDesc('created_at')->paginate($perPage);

        // Kirim data ke view
        return view('admin.menuMakanan.menuMakanan', compact('menus', 'perPage'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.menuMakanan.addMenuMakanan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1) Validasi input sesuai form (tanpa menu_number)
        $validated = $request->validate([
            'nama_menu'      => ['nullable', 'string', 'max:150'],
            'batch'          => ['nullable', 'string', 'max:10'],

            // serve_days dikirim sebagai JSON string oleh JS
            'serve_days'     => ['required', 'string'],

            // Upload foto
            'foto_makanan'   => ['nullable', 'array', 'max:5'],
            'foto_makanan.*' => ['image', 'mimes:jpeg,jpg,png', 'max:2048'],

            // Input dinamis (array string)
            'makan_siang'    => ['sometimes', 'array'],
            'makan_siang.*'  => ['nullable', 'string', 'max:200'],
            'makan_malam'    => ['sometimes', 'array'],
            'makan_malam.*'  => ['nullable', 'string', 'max:200'],
        ], [
            'serve_days.required'  => 'Serve days belum terbentuk, silakan pilih menu.',
            'foto_makanan.max'    => 'Maksimal upload 5 foto.',
            'foto_makanan.*.image' => 'File harus berupa gambar.',
            'foto_makanan.*.mimes' => 'Format foto harus jpeg, jpg, atau png.',
            'foto_makanan.*.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // 2) Upload foto jika ada
        $fotoPaths = [];
        $fotoLabels = $request->input('foto_label', []);
        if ($request->hasFile('foto_makanan')) {
            foreach ($request->file('foto_makanan') as $index => $file) {
                if ($file->isValid()) {
                    $path = $file->store('menu_makanan', 'public');
                    $fotoPaths[] = [
                        'path'  => $path,
                        'label' => $fotoLabels[$index] ?? '',
                    ];
                }
            }
        }

        // 3) Parse & validasi serve_days (JSON -> array angka)
        $serveDays = json_decode($validated['serve_days'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($serveDays)) {
            return back()->withInput()->withErrors([
                'serve_days' => 'Format serve_days tidak valid.',
            ]);
        }

        // Normalisasi: integer unik, urut, 1..31
        $serveDays = array_values(array_unique(array_map('intval', $serveDays)));
        sort($serveDays);

        // 4) Infer nomor menu dari serve_days:
        // - Jika [31]  -> menu = 11
        // - Selain itu  -> menu = hari pertama (harus pola [n, n+10, n+20] & <=31)
        $inferredMenu = null;
        $validPattern = false;

        if (count($serveDays) === 1 && $serveDays[0] === 31) {
            $inferredMenu = 11;
            $validPattern = true;
        } elseif (count($serveDays) === 3) {
            [$a, $b, $c] = $serveDays;
            // cek pola [n, n+10, n+20] untuk 1..10
            if ($a >= 1 && $a <= 10 && $b === $a + 10 && $c === $a + 20 && $c <= 31) {
                $inferredMenu = $a;
                $validPattern = true;
            }
        }

        if (!$validPattern) {
            return back()->withInput()->withErrors([
                'serve_days' => 'Tanggal serve_days tidak sesuai pola pilihan menu.',
            ]);
        }

        // 5) Bentuk spesifikasi menu dari input dinamis
        $siang = array_values(array_filter(
            array_map('trim', (array) $request->input('makan_siang', [])),
            'strlen'
        ));
        $malam = array_values(array_filter(
            array_map('trim', (array) $request->input('makan_malam', [])),
            'strlen'
        ));

        if (count($siang) + count($malam) === 0) {
            return back()->withInput()->withErrors([
                'makan_siang' => 'Minimal satu item menu harus diisi (siang atau malam).'
            ]);
        }

        $spec = [
            'Makan Siang' => $siang,
            'Makan Malam' => $malam,
        ];

        // 6) Nama menu: pakai hidden kalau ada; jika kosong, auto dari hasil infer
        $namaMenu = $validated['nama_menu'] ?: ('Menu ' . $inferredMenu);

        // 7) Simpan
        MenuMakanan::create([
            'nama_menu'  => $namaMenu,
            'batch'      => $validated['batch'] ?? null,
            'serve_days' => $serveDays, // pastikan casts di model
            'spec_menu'  => $spec,
            'foto_makanan' => $fotoPaths, // simpan array path foto
            'created_by' => Auth::id(),
        ]);

        // 8) Redirect
        return redirect()
            ->route('admin.menuMakanan') // atau index kalau itu yang kamu pakai
            ->with('success', 'Menu berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(menuMakanan $menuMakanan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $menuMakanan = MenuMakanan::findOrFail($id);
        return view('admin.menuMakanan.editMenuMakanan', compact('menuMakanan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuMakanan $menuMakanan)
    {
        // 1) Validasi input (tanpa menu_number)
        $validated = $request->validate([
            'nama_menu'      => ['nullable', 'string', 'max:150'],
            'batch'          => ['nullable', 'string', 'max:10'],

            // serve_days dikirim sebagai JSON string oleh JS (hidden)
            'serve_days'     => ['required'], // terima string atau array

            // Upload foto
            'foto_makanan'   => ['nullable', 'array', 'max:5'],
            'foto_makanan.*' => ['image', 'mimes:jpeg,jpg,png', 'max:2048'],

            // Input dinamis (array string)
            'makan_siang'    => ['sometimes', 'array'],
            'makan_siang.*'  => ['nullable', 'string', 'max:200'],
            'makan_malam'    => ['sometimes', 'array'],
            'makan_malam.*'  => ['nullable', 'string', 'max:200'],
        ], [
            'serve_days.required'  => 'Serve days belum terbentuk, silakan pilih menu.',
            'foto_makanan.max'    => 'Maksimal upload 5 foto.',
            'foto_makanan.*.image' => 'File harus berupa gambar.',
            'foto_makanan.*.mimes' => 'Format foto harus jpeg, jpg, atau png.',
            'foto_makanan.*.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // 2) Upload foto jika ada dan hapus foto yang dipilih
        $fotoPaths = $menuMakanan->foto_makanan ?? []; // Ambil foto lama

        // Normalize old format (plain strings) to new format (objects)
        $fotoPaths = array_map(function ($item) {
            if (is_string($item)) {
                return ['path' => $item, 'label' => ''];
            }
            return $item;
        }, $fotoPaths);

        // Update labels of existing photos
        $existingLabels = $request->input('existing_foto_label', []);
        foreach ($existingLabels as $idx => $label) {
            if (isset($fotoPaths[$idx])) {
                $fotoPaths[$idx]['label'] = $label ?? '';
            }
        }
        
        // Hapus foto yang dipilih user
        $removedFotos = $request->input('removed_fotos');
        if ($removedFotos) {
            $removedFotosArray = json_decode($removedFotos, true) ?: [];
            foreach ($removedFotosArray as $removedFoto) {
                // Hapus dari storage
                $fullPath = storage_path('app/public/' . $removedFoto);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                
                // Hapus dari array (cari berdasarkan path)
                $fotoPaths = array_filter($fotoPaths, function ($item) use ($removedFoto) {
                    $path = is_array($item) ? ($item['path'] ?? '') : $item;
                    return $path !== $removedFoto;
                });
            }
            $fotoPaths = array_values($fotoPaths);
        }
        
        // Upload foto baru jika ada (handle array individual)
        $fotoFiles = $request->file('foto_makanan', []);
        $fotoLabels = $request->input('foto_label', []);
        if ($fotoFiles) {
            if (!is_array($fotoFiles)) {
                $fotoFiles = [$fotoFiles];
            }
            
            foreach ($fotoFiles as $index => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('menu_makanan', 'public');
                    $fotoPaths[] = [
                        'path'  => $path,
                        'label' => $fotoLabels[$index] ?? '',
                    ];
                }
            }
        }

        // 3) Parse & validasi serve_days -> array angka
        $serveInput = $validated['serve_days'];

        if (is_string($serveInput)) {
            $serveDays = json_decode($serveInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors([
                    'serve_days' => 'Format serve_days tidak valid (bukan JSON).',
                ]);
            }
        } elseif (is_array($serveInput)) {
            // antisipasi kalau ada form lain yang kirim sebagai array langsung
            $serveDays = $serveInput;
        } else {
            return back()->withInput()->withErrors([
                'serve_days' => 'Tipe serve_days tidak dikenali.',
            ]);
        }

        if (!is_array($serveDays)) {
            return back()->withInput()->withErrors([
                'serve_days' => 'Format serve_days tidak valid.',
            ]);
        }

        // Normalisasi: integer unik, urut, dalam 1..31
        $serveDays = array_values(array_unique(array_map('intval', $serveDays)));
        sort($serveDays);

        // 4) Infer nomor menu dari serve_days
        $inferredMenu = null;
        $validPattern = false;

        if (count($serveDays) === 1 && $serveDays[0] === 31) {
            $inferredMenu = 11;
            $validPattern = true;
        } elseif (count($serveDays) === 3) {
            [$a, $b, $c] = $serveDays;
            if ($a >= 1 && $a <= 10 && $b === $a + 10 && $c === $a + 20 && $c <= 31) {
                $inferredMenu = $a;
                $validPattern = true;
            }
        }

        if (!$validPattern) {
            return back()->withInput()->withErrors([
                'serve_days' => 'Tanggal serve_days tidak sesuai pola pilihan menu.',
            ]);
        }

        // 5) Bentuk spesifikasi menu dari input dinamis
        $siang = array_values(array_filter(
            array_map('trim', (array) $request->input('makan_siang', [])),
            'strlen'
        ));
        $malam = array_values(array_filter(
            array_map('trim', (array) $request->input('makan_malam', [])),
            'strlen'
        ));

        if (count($siang) + count($malam) === 0) {
            return back()->withInput()->withErrors([
                'makan_siang' => 'Minimal satu item menu harus diisi (siang atau malam).'
            ]);
        }

        $spec = [
            'Makan Siang' => $siang,
            'Makan Malam' => $malam,
        ];

        // 6) Nama menu:
        // - Jika ada input, pakai input
        // - Jika kosong, pertahankan yang lama; jika lama kosong juga, auto dari infer
        $namaMenu = $validated['nama_menu']
            ?: ($menuMakanan->nama_menu ?: ('Menu ' . $inferredMenu));

        // 7) Update
        $menuMakanan->update([
            'nama_menu'  => $namaMenu,
            'batch'      => $validated['batch'] ?? null,
            'serve_days' => $serveDays, // model casts ke array
            'spec_menu'  => $spec,      // model casts ke array
            'foto_makanan' => $fotoPaths, // update foto
            'updated_by' => Auth::id(),
            
        ]);

        // 8) Redirect
        return redirect()
            ->route('admin.menuMakanan') // <-- sesuaikan jika nama route index-mu berbeda
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuMakanan $menuMakanan)
    {
        try {
            $menuMakanan->update(['deleted_by' => Auth::id()]);
            $menuMakanan->delete(); // soft delete

            return redirect()
                ->route('admin.menuMakanan')
                ->with('success', 'Menu "' . $menuMakanan->nama_menu . '" berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
