<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerController extends Controller
{
    // Seed awal ke session kalau belum ada
    private function seedDummyIfNeeded(): void
    {
        if (!session()->has('customers_dummy')) {
            $data = [
                // tambahkan id unik
                ['id' => 1, 'nama' => 'Ahmad Fauzi', 'nik' => '3276012300010001', 'alamat' => 'Jl. Melati No.12, Bandung', 'jenis_kelamin' => 'Laki-laki', 'ttl' => 'Bandung, 12 Mei 1995', 'bb_tb' => '65kg / 170cm', 'email' => 'ahmad.fauzi@example.com', 'hp' => '081234567890'],
                ['id' => 2, 'nama' => 'Siti Rahma', 'nik' => '3204021500020002', 'alamat' => 'Jl. Mawar No.45, Jakarta', 'jenis_kelamin' => 'Perempuan', 'ttl' => 'Jakarta, 20 Agustus 1997', 'bb_tb' => '52kg / 160cm', 'email' => 'siti.rahma@example.com', 'hp' => '082345678901'],
                ['id' => 3, 'nama' => 'Budi Santoso', 'nik' => '3517090900030003', 'alamat' => 'Jl. Kenanga No.7, Surabaya', 'jenis_kelamin' => 'Laki-laki', 'ttl' => 'Surabaya, 9 September 1993', 'bb_tb' => '70kg / 175cm', 'email' => 'budi.santoso@example.com', 'hp' => '083456789012'],
                ['id' => 4, 'nama' => 'Dewi Lestari', 'nik' => '3175091200040004', 'alamat' => 'Jl. Anggrek No.9, Depok', 'jenis_kelamin' => 'Perempuan', 'ttl' => 'Depok, 1 Januari 1996', 'bb_tb' => '55kg / 162cm', 'email' => 'dewi.lestari@example.com', 'hp' => '081987654321'],
                ['id' => 5, 'nama' => 'Rizal Hakim', 'nik' => '3276012300050005', 'alamat' => 'Jl. Teratai No.3, Bekasi', 'jenis_kelamin' => 'Laki-laki', 'ttl' => 'Bekasi, 14 Juli 1992', 'bb_tb' => '80kg / 180cm', 'email' => 'rizal.hakim@example.com', 'hp' => '082112223333'],
                ['id' => 6, 'nama' => 'Indah Pratiwi', 'nik' => '3276012300060006', 'alamat' => 'Jl. Cemara No.5, Bandung', 'jenis_kelamin' => 'Perempuan', 'ttl' => 'Bandung, 2 Februari 1998', 'bb_tb' => '50kg / 158cm', 'email' => 'indah.pratiwi@example.com', 'hp' => '081200000001'],
                ['id' => 7, 'nama' => 'Fajar Nugraha', 'nik' => '3276012300070007', 'alamat' => 'Jl. Pahlawan No.10, Yogyakarta', 'jenis_kelamin' => 'Laki-laki', 'ttl' => 'Yogyakarta, 17 Maret 1994', 'bb_tb' => '75kg / 176cm', 'email' => 'fajar.nugraha@example.com', 'hp' => '081200000002'],
                ['id' => 8, 'nama' => 'Lala Sari', 'nik' => '3276012300080008', 'alamat' => 'Jl. Merdeka No.8, Cimahi', 'jenis_kelamin' => 'Perempuan', 'ttl' => 'Cimahi, 9 April 1999', 'bb_tb' => '48kg / 155cm', 'email' => 'lala.sari@example.com', 'hp' => '081200000003'],
                ['id' => 9, 'nama' => 'Yoga Prasetyo', 'nik' => '3276012300090009', 'alamat' => 'Jl. Garuda No.21, Jakarta', 'jenis_kelamin' => 'Laki-laki', 'ttl' => 'Jakarta, 30 Juni 1991', 'bb_tb' => '68kg / 172cm', 'email' => 'yoga.prasetyo@example.com', 'hp' => '081200000004'],
                ['id' => 10, 'nama' => 'Ayu Wulandari', 'nik' => '3276012300100010', 'alamat' => 'Jl. Flamboyan No.2, Bogor', 'jenis_kelamin' => 'Perempuan', 'ttl' => 'Bogor, 15 November 1996', 'bb_tb' => '53kg / 161cm', 'email' => 'ayu.wulandari@example.com', 'hp' => '081200000005'],
                // ... lanjutkan data lainmu, pastikan id unik
            ];
            session(['customers_dummy' => $data]);
        }
    }

    public function index(Request $request)
    {
        $this->seedDummyIfNeeded();

        // Ambil dari session
        $data = session('customers_dummy', []);

        // === SEARCH (q) & PAGE SIZE (per_page) ===
        $q       = trim($request->query('q', ''));
        $perPage = (int) $request->query('per_page', 5);
        if ($perPage <= 0) $perPage = 5;

        // Filter collection dengan kolom apa pun
        $filtered = collect($data)->when($q !== '', function ($c) use ($q) {
            $qLower = mb_strtolower($q);
            return $c->filter(function ($row) use ($qLower) {
                return collect($row)->contains(function ($val) use ($qLower) {
                    return str_contains(mb_strtolower((string)$val), $qLower);
                });
            })->values();
        }, fn($c) => $c);

        // === PAGINATION (manual collection) ===
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items       = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.customers.customers', [
            'customers' => $paginator,
            'q'         => $q,
            'perPage'   => $perPage,
        ]);
    }

    public function destroy($id, Request $request)
    {
        // Ambil data dari session
        $data = session('customers_dummy', []);

        // Hapus berdasarkan id (string compare biar aman)
        $data = array_values(array_filter($data, function ($row) use ($id) {
            return (string)($row['id'] ?? '') !== (string)$id;
        }));

        // Simpan balik ke session
        session(['customers_dummy' => $data]);

        // Kembali ke index, pertahankan query string
        return redirect()
            ->route('customers.index', $request->only('q', 'per_page', 'page'))
            ->with('status', 'Data customer telah dihapus.');
    }

    /** Ambil ID berikutnya dari array session */
    private function nextId(array $rows): int
    {
        return empty($rows) ? 1 : (max(array_column($rows, 'id')) + 1);
    }

    /** FORM CREATE */
    public function create(Request $request)
    {
        $this->seedDummyIfNeeded();

        return view('admin.customers.customers-create'); // view baru
    }

    /** SIMPAN DATA BARU (dummy ke session) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'nik'           => 'required|string|max:30',
            'alamat'        => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir'  => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'bb_tb'         => 'nullable|string|max:50',
            'email'         => 'required|email',
            'hp'            => 'required|string|max:20',
        ]);

        // gabung ttl
        $validated['ttl'] = $validated['tempat_lahir'] . ', ' . \Carbon\Carbon::parse($validated['tanggal_lahir'])->translatedFormat('d F Y');

        unset($validated['tempat_lahir'], $validated['tanggal_lahir']);

        // ambil data lama
        $customers = session('customers_dummy', []);
        $newId     = count($customers) ? max(array_column($customers, 'id')) + 1 : 1;

        $validated['id'] = $newId;

        $customers[] = $validated;
        session(['customers_dummy' => $customers]);

        return redirect()->route('customers.index')->with('status', 'Customer berhasil ditambahkan');
    }


    /** FORM EDIT */
    public function edit($id, Request $request)
    {
        $this->seedDummyIfNeeded();

        $data = session('customers_dummy', []);
        $found = collect($data)->firstWhere('id', (int)$id);

        if (!$found) {
            abort(404, 'Customer tidak ditemukan');
        }

        return view('customers-edit', ['customer' => $found]); // view baru
    }

    /** UPDATE DATA */
    public function update($id, Request $request)
    {
        $this->seedDummyIfNeeded();

        $data = session('customers_dummy', []);

        // Validasi sama seperti store
        $validated = $request->validate([
            'nama'           => ['required', 'string', 'max:100'],
            'nik'            => ['required', 'string', 'max:20'],
            'alamat'         => ['required', 'string', 'max:255'],
            'jenis_kelamin'  => ['required', 'in:Laki-laki,Perempuan'],
            'ttl'            => ['required', 'string', 'max:100'],
            'bb_tb'          => ['nullable', 'string', 'max:50'],
            'email'          => ['required', 'email', 'max:100'],
            'hp'             => ['required', 'string', 'max:20'],
        ]);

        // Update di array
        foreach ($data as &$row) {
            if ((string)$row['id'] === (string)$id) {
                $row = array_merge($row, $validated); // id tetap
                break;
            }
        }
        unset($row);

        session(['customers_dummy' => $data]);

        // Pertahankan query (opsional)
        return redirect()
            ->route('customers.index', $request->only('q', 'per_page', 'page'))
            ->with('status', 'Data customer diperbarui.');
    }
}
