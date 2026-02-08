<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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
        $q       = trim((string) $request->input('q'));
        $perPage = (int) $request->input('per_page', 10);

        $petugas = User::query()
            ->where('role', '!=', 'customer')
            ->with(['detail:id,user_id,mr,nik']) // biar bisa akses $c->detail tanpa N+1
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($b) use ($q) {
                    $b->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhereHas('detail', function ($d) use ($q) {
                            $d->where('mr', 'like', "%{$q}%")
                                ->orWhere('nik', 'like', "%{$q}%");
                        });
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
        $this->seedDummyIfNeeded();
        return view('admin.petugas.petugas-create');
    }

    /** STORE (simpan data baru ke session) */
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
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'  => 'required|string|in:admin,ahli_gizi,medical_record,bendahara',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.data.petugas')
            ->with('success', 'Data petugas berhasil diperbarui!');
    }
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'bb_tb'         => $request->bb_tb,
            'hp'            => $request->hp,
            'usia'          => $request->usia,
            'updated_by'    => Auth::id(),
        ]);

        return back()->with('success', 'Data petugas berhasil diperbarui.');
    }


    /** DELETE */
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            if (Schema::hasColumn($user->getTable(), 'deleted_by')) {
                // updateQuietly supaya tidak trigger event yang aneh
                $user->forceFill(['deleted_by' => Auth::id()])->saveQuietly();
            }

            if (method_exists($user, 'detail')) {
                $detail = $user->detail()->first();
                if ($detail) {
                    if (Schema::hasColumn($detail->getTable(), 'deleted_by')) {
                        $detail->forceFill(['deleted_by' => Auth::id()])->saveQuietly();
                    }
                    $detail->delete(); // soft delete detail
                }
            }

            $user->delete(); // soft delete user
        });

        return redirect()
            ->route('admin.data.petugas') // ini tadinya ke customers, kemungkinan typo
            ->with('status', 'User berhasil dihapus.');
    }
}
