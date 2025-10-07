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
            'nik'           => 'nullable|string|max:50',
            'alamat'        => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'hp'            => 'nullable|string|max:25',
        ]);

        // Buat akun user dengan password default
        $user = User::create([
            'name'       => $request->nama,
            'role'       => $request->role,
            'email'      => $request->email,
            'password'   => Hash::make('password123!'),
            'created_by' => Auth::id(),
        ]);


        // Kalau ada tabel `user_details`, bisa simpan data tambahan di sana
        if (class_exists(UserDetail::class)) {
            UserDetail::create([
                'user_id'       => $user->id,
                'nik'           => $request->nik,
                'alamat'        => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir'  => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'bb_tb'         => $request->bb_tb,   // <<— penting!
                'hp'            => $request->hp,
                'created_by'    => Auth::id(),
            ]);
        }

        return redirect()
            ->route('admin.data.petugas')
            ->with('success', 'Akun petugas berhasil dibuat dengan password default: password123!');
    }

    public function show(UserDetail $user_detail)
    {
        // ambil relasi user
        $user_detail->load('user:id,name,email,role,password');

        // decrypt foto ktp (kalau pakai manual Crypt::encryptString)
        $fotoKtp = null;
        if (!empty($user_detail->foto_ktp_base64)) {
            try {
                // kalau pakai cast 'encrypted', ini sudah otomatis plaintext base64
                $fotoKtp = $user_detail->foto_ktp_base64;

                // kalau kamu simpan manual pakai Crypt::encryptString(), gunakan:
                // $fotoKtp = Crypt::decryptString($user_detail->foto_ktp_base64);
            } catch (\Exception $e) {
                $fotoKtp = null;
            }
        }

        return view('admin.petugas.petugas-detail', [
            'detail'   => $user_detail,
            'fotoKtp'  => $fotoKtp,
        ]);
    }
    /** FORM EDIT */
    public function edit(UserDetail $user_detail)
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        // siapkan base64 untuk preview
        $fotoKtp = null;
        if (!empty($user_detail->foto_ktp_base64)) {
            try {
                $fotoKtp = $user_detail->foto_ktp_base64; // kalau tidak dienkripsi manual
                // jika dulunya disimpan pakai Crypt::encryptString(), pakai:
                // $fotoKtp = Crypt::decryptString($user_detail->foto_ktp_base64);
            } catch (\Exception $e) {
                $fotoKtp = null;
            }
        }

        return view('admin.petugas.details.edit', [
            'detail'  => $user_detail,
            'users'   => $users,
            'fotoKtp' => $fotoKtp, // <<— penting untuk preview
        ]);
    }

    /** UPDATE DATA */
    public function update(Request $request, UserDetail $user_detail)
    {
        // update user
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user_detail->user_id,
            'role'  => 'required|string|in:admin,ahli_gizi,medical_record,bendahara',
            // validasi detail opsional:
            'mr'            => 'nullable|string|max:100',
            'nik'           => 'nullable|string|max:50',
            'alamat'        => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'bb_tb'         => 'nullable|string', // format "60/170"
            'hp'            => 'nullable|string|max:25',
            'usia'          => 'nullable|integer|min:0',
        ]);

        $user = $user_detail->user;
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        $user_detail->update([
            'mr'            => $request->mr,
            'nik'           => $request->nik,
            'alamat'        => $request->alamat,
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
