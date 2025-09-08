<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PetugasController extends Controller
{
    /** Seed awal ke session kalau belum ada */
    private function seedDummyIfNeeded(): void
    {
        if (!session()->has('petugas_dummy')) {
            $data = [
                [
                    'id' => 1,
                    'nama' => 'Arman Saputra',
                    'nik' => '1001001001000001',
                    'alamat' => 'Jl. Angkasa No.10, Bandung',
                    'jenis_kelamin' => 'Laki-laki',
                    'ttl' => 'Bandung, 5 Maret 1990',
                    'posisi_jabatan' => 'Super Admin',
                    'email' => 'arman.saputra@example.com',
                    'hp' => '081111111111'
                ],
                [
                    'id' => 2,
                    'nama' => 'Bella Kartika',
                    'nik' => '1001001001000002',
                    'alamat' => 'Jl. Bunga Raya No.20, Jakarta',
                    'jenis_kelamin' => 'Perempuan',
                    'ttl' => 'Jakarta, 12 Juli 1993',
                    'posisi_jabatan' => 'Admin',
                    'email' => 'bella.kartika@example.com',
                    'hp' => '082222222222'
                ],
                [
                    'id' => 3,
                    'nama' => 'Cahyo Nugroho',
                    'nik' => '1001001001000003',
                    'alamat' => 'Jl. Merdeka No.5, Surabaya',
                    'jenis_kelamin' => 'Laki-laki',
                    'ttl' => 'Surabaya, 9 September 1988',
                    'posisi_jabatan' => 'Petugas',
                    'email' => 'cahyo.nugroho@example.com',
                    'hp' => '083333333333'
                ],
                [
                    'id' => 4,
                    'nama' => 'Dian Puspitasari',
                    'nik' => '1001001001000004',
                    'alamat' => 'Jl. Cemara No.15, Yogyakarta',
                    'jenis_kelamin' => 'Perempuan',
                    'ttl' => 'Yogyakarta, 21 Januari 1995',
                    'posisi_jabatan' => 'Staf',
                    'email' => 'dian.puspitasari@example.com',
                    'hp' => '084444444444'
                ],
                [
                    'id' => 5,
                    'nama' => 'Eko Pratama',
                    'nik' => '1001001001000005',
                    'alamat' => 'Jl. Kenari No.7, Medan',
                    'jenis_kelamin' => 'Laki-laki',
                    'ttl' => 'Medan, 30 Mei 1992',
                    'posisi_jabatan' => 'Admin',
                    'email' => 'eko.pratama@example.com',
                    'hp' => '085555555555'
                ],
            ];
            session(['petugas_dummy' => $data]);
        }
    }

    /** Ambil ID berikutnya dari array session */
    private function nextId(array $rows): int
    {
        return empty($rows) ? 1 : (max(array_column($rows, 'id')) + 1);
    }

    /** LIST + SEARCH + PAGINATION */
    public function index(Request $request)
    {
        $this->seedDummyIfNeeded();

        $data = session('petugas_dummy', []);

        // === SEARCH (q) & PAGE SIZE (per_page) ===
        $q       = trim($request->query('q', ''));
        $perPage = (int) $request->query('per_page', 5);
        if ($perPage <= 0) $perPage = 5;

        // Filter semua kolom
        $filtered = collect($data)->when($q !== '', function ($c) use ($q) {
            $qLower = mb_strtolower($q);
            return $c->filter(function ($row) use ($qLower) {
                return collect($row)->contains(function ($val) use ($qLower) {
                    return str_contains(mb_strtolower((string)$val), $qLower);
                });
            })->values();
        }, fn($c) => $c);

        // Pagination manual
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items       = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.petugas.petugas', [
            'petugas' => $paginator,
            'q'       => $q,
            'perPage' => $perPage,
        ]);
    }

    /** FORM CREATE */
    public function create(Request $request)
    {
        $this->seedDummyIfNeeded();
        return view('admin.petugas.petugas-create');
    }

    /** STORE (simpan data baru ke session) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'           => 'required|string|max:255',
            'nik'            => 'required|string|max:30',
            'alamat'         => 'required|string',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir'   => 'required|string|max:100',
            'tanggal_lahir'  => 'required|date',
            'posisi_jabatan' => 'required|string|max:100',
            'email'          => 'required|email',
            'hp'             => 'required|string|max:20',
        ]);

        // gabung ttl (tempat, tgl)
        $validated['ttl'] = $validated['tempat_lahir'] . ', ' . \Carbon\Carbon::parse($validated['tanggal_lahir'])->translatedFormat('d F Y');
        unset($validated['tempat_lahir'], $validated['tanggal_lahir']);

        // Ambil data lama & next id
        $rows  = session('petugas_dummy', []);
        $newId = count($rows) ? max(array_column($rows, 'id')) + 1 : 1;
        $validated['id'] = $newId;

        $rows[] = $validated;
        session(['petugas_dummy' => $rows]);

        return redirect()->route('petugas.index')->with('status', 'Petugas berhasil ditambahkan.');
    }

    /** FORM EDIT */
    public function edit($id, Request $request)
    {
        $this->seedDummyIfNeeded();

        $rows  = session('petugas_dummy', []);
        $found = collect($rows)->firstWhere('id', (int)$id);

        if (!$found) {
            abort(404, 'Petugas tidak ditemukan');
        }

        return view('petugas-edit', ['petugas' => $found]);
    }

    /** UPDATE DATA */
    public function update($id, Request $request)
    {
        $this->seedDummyIfNeeded();

        $rows = session('petugas_dummy', []);

        // Validasi mirip store, tetapi di edit ttl langsung string (biar sama dengan customers.update milikmu)
        $validated = $request->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'nik'            => ['required', 'string', 'max:30'],
            'alamat'         => ['required', 'string'],
            'jenis_kelamin'  => ['required', 'in:Laki-laki,Perempuan'],
            'ttl'            => ['required', 'string', 'max:100'],
            'posisi_jabatan' => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:100'],
            'hp'             => ['required', 'string', 'max:20'],
        ]);

        foreach ($rows as &$row) {
            if ((string)($row['id'] ?? '') === (string)$id) {
                $row = array_merge($row, $validated);
                break;
            }
        }
        unset($row);

        session(['petugas_dummy' => $rows]);

        return redirect()
            ->route('petugas.index', $request->only('q', 'per_page', 'page'))
            ->with('status', 'Data petugas diperbarui.');
    }

    /** DELETE */
    public function destroy($id, Request $request)
    {
        $rows = session('petugas_dummy', []);

        $rows = array_values(array_filter($rows, function ($row) use ($id) {
            return (string)($row['id'] ?? '') !== (string)$id;
        }));

        session(['petugas_dummy' => $rows]);

        return redirect()
            ->route('petugas.index', $request->only('q', 'per_page', 'page'))
            ->with('status', 'Data petugas telah dihapus.');
    }
}
