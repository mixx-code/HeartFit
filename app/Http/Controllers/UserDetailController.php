<?php

namespace App\Http\Controllers;

use App\Models\UserDetail;
use App\Http\Requests\StoreUserDetailRequest;
use App\Http\Requests\UpdateUserDetailRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserDetailController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q'));
        $perPage = (int) $request->input('per_page', 10);

        $details = DB::table('user_details')
            ->join('users', 'users.id', '=', 'user_details.user_id')
            ->select(
                'user_details.*',
                'users.name',
                'users.email',
                'users.role'
            )
            ->when($q, function ($b) use ($q) {
                $b->where(function ($qbuilder) use ($q) {
                    $qbuilder->where('user_details.mr', 'like', "%{$q}%")
                        ->orWhere('user_details.nik', 'like', "%{$q}%")
                        ->orWhere('users.name', 'like', "%{$q}%")
                        ->orWhere('users.email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('user_details.id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.customers.details.index', compact('details', 'perPage'));
    }

    public function create()
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        return view('admin.customers.details.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mr'               => ['required', 'string', 'max:50', 'unique:user_details,mr'],
            'nik'              => ['required', 'string', 'max:32', 'unique:user_details,nik'],
            'alamat'           => ['required', 'string'],
            'jenis_kelamin'    => ['required', 'in:L,P'],
            'tempat_lahir'     => ['required', 'string', 'max:100'],
            'tanggal_lahir'    => ['required', 'date'],
            'bb_tb'            => ['nullable', 'string', 'max:20'],
            'foto_ktp_base64'  => ['nullable', 'string'],       // jika dikirim base64 langsung dari FE
            'foto_ktp'         => ['nullable', 'file', 'image', 'max:2048'], // jika upload file (2MB)
            'hp'               => ['nullable', 'string', 'max:30'],
            'usia'             => ['nullable', 'integer', 'min:0', 'max:150'],

            // akun login
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        DB::transaction(function () use ($data, $request) {
            // 1) buat akun
            $user = User::create([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'role'       => 'customer',
                'password'   => bcrypt('password123!'),
                'created_by' => Auth::id(),
            ]);

            // 2) siapkan data detail
            $detailData = collect($data)->except(['name', 'email', 'foto_ktp', 'foto_ktp_base64'])->toArray();
            $detailData['user_id']    = $user->id;
            $detailData['created_by'] = Auth::id();

            // 3) isi foto_ktp_base64 dari file atau dari field base64
            $fotoBase64 = null;

            // a) jika user upload file
            if ($request->hasFile('foto_ktp') && $request->file('foto_ktp')->isValid()) {
                $mime = $request->file('foto_ktp')->getMimeType(); // e.g. image/png
                $bin  = file_get_contents($request->file('foto_ktp')->getRealPath());
                $fotoBase64 = 'data:' . $mime . ';base64,' . base64_encode($bin);
            }
            // b) jika FE kirim base64 langsung
            elseif (!empty($data['foto_ktp_base64'])) {
                // opsional: normalisasi, pastikan ada prefix data:...;base64,
                $raw = $data['foto_ktp_base64'];
                $fotoBase64 = Str::startsWith($raw, 'data:') ? $raw : ('data:image/png;base64,' . $raw);
            }

            $detailData['foto_ktp_base64'] = $fotoBase64;

            UserDetail::create($detailData);
        });

        return redirect()
            ->route('admin.data.customers')
            ->with('status', 'Detail + akun user berhasil dibuat.');
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

        return view('admin.customers.customer-detail', [
            'detail'   => $user_detail,
            'fotoKtp'  => $fotoKtp,
        ]);
    }
    public function showAkun(UserDetail $user_detail)
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

        return view('customers.akun.akun', [
            'detail'   => $user_detail,
            'fotoKtp'  => $fotoKtp,
        ]);
    }

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

        return view('admin.customers.details.edit', [
            'detail'  => $user_detail,
            'users'   => $users,
            'fotoKtp' => $fotoKtp, // <<— penting untuk preview
        ]);
    }


    public function update(Request $request, UserDetail $user_detail)
    {
        // samakan rules dengan store:
        $data = $request->validate([
            'mr'               => ['required', 'string', 'max:50', 'unique:user_details,mr,' . $user_detail->id],
            'nik'              => ['required', 'string', 'max:32', 'unique:user_details,nik,' . $user_detail->id],
            'alamat'           => ['required', 'string'],
            'jenis_kelamin'    => ['required', 'in:L,P'],
            'tempat_lahir'     => ['required', 'string', 'max:100'],
            'tanggal_lahir'    => ['required', 'date'],
            'bb_tb'            => ['nullable', 'string', 'max:20'],
            'foto_ktp_base64'  => ['nullable', 'string'],                 // sama seperti store
            'foto_ktp'         => ['nullable', 'file', 'image', 'max:2048'], // 2MB (sama dengan store)
            'hp'               => ['nullable', 'string', 'max:30'],
            'usia'             => ['nullable', 'integer', 'min:0', 'max:150'],

            // akun login (samakan dengan store, tapi unique email diabaikan untuk user saat ini)
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users,email,' . $user_detail->user_id],
        ]);

        DB::transaction(function () use ($data, $request, $user_detail) {
            // 1) update akun user (name & email)
            $user = $user_detail->user; // pastikan relasi ada: UserDetail belongsTo User
            $user->update([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'updated_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            // 2) siapkan payload untuk user_details
            $payload = collect($data)->except(['name', 'email', 'foto_ktp'])->toArray();
            $payload['updated_by'] = \Illuminate\Support\Facades\Auth::id();

            // 3) tentukan foto_ktp_base64 (file / base64 / biarkan lama)
            $newBase64 = null;

            // a) jika upload file
            if ($request->hasFile('foto_ktp') && $request->file('foto_ktp')->isValid()) {
                $mime = $request->file('foto_ktp')->getMimeType(); // image/png atau image/jpeg
                $bin  = file_get_contents($request->file('foto_ktp')->getRealPath());
                $newBase64 = 'data:' . $mime . ';base64,' . base64_encode($bin);
            }
            // b) jika FE kirim base64 langsung
            elseif (!empty($data['foto_ktp_base64'])) {
                $raw = $data['foto_ktp_base64'];
                $newBase64 = \Illuminate\Support\Str::startsWith($raw, 'data:')
                    ? $raw
                    : ('data:image/png;base64,' . $raw);
            }

            // hanya set kalau ada yang baru; kalau tidak ada, jangan timpa yg lama
            if (!is_null($newBase64)) {
                $payload['foto_ktp_base64'] = $newBase64;
            } else {
                unset($payload['foto_ktp_base64']);
            }

            // 4) update detail
            $user_detail->update($payload);
        });

        return redirect()->route('admin.data.customer.detail')->with('status', 'Detail user berhasil diperbarui.');
    }

    public function updateAkun(Request $request, UserDetail $user_detail)
    {
        // samakan rules dengan store:
        $data = $request->validate([
            'mr'               => ['required', 'string', 'max:50', 'unique:user_details,mr,' . $user_detail->id],
            'nik'              => ['required', 'string', 'max:32', 'unique:user_details,nik,' . $user_detail->id],
            'alamat'           => ['required', 'string'],
            'jenis_kelamin'    => ['required', 'in:L,P'],
            'tempat_lahir'     => ['required', 'string', 'max:100'],
            'tanggal_lahir'    => ['required', 'date'],
            'bb_tb'            => ['nullable', 'string', 'max:20'],
            'foto_ktp_base64'  => ['nullable', 'string'],                 // sama seperti store
            'foto_ktp'         => ['nullable', 'file', 'image', 'max:2048'], // 2MB (sama dengan store)
            'hp'               => ['nullable', 'string', 'max:30'],
            'usia'             => ['nullable', 'integer', 'min:0', 'max:150'],

            // akun login (samakan dengan store, tapi unique email diabaikan untuk user saat ini)
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users,email,' . $user_detail->user_id],
        ]);

        DB::transaction(function () use ($data, $request, $user_detail) {
            // 1) update akun user (name & email)
            $user = $user_detail->user; // pastikan relasi ada: UserDetail belongsTo User
            $user->update([
                'name'       => $data['name'],
                'email'      => $data['email'],
                'updated_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            // 2) siapkan payload untuk user_details
            $payload = collect($data)->except(['name', 'email', 'foto_ktp'])->toArray();
            $payload['updated_by'] = \Illuminate\Support\Facades\Auth::id();

            // 3) tentukan foto_ktp_base64 (file / base64 / biarkan lama)
            $newBase64 = null;

            // a) jika upload file
            if ($request->hasFile('foto_ktp') && $request->file('foto_ktp')->isValid()) {
                $mime = $request->file('foto_ktp')->getMimeType(); // image/png atau image/jpeg
                $bin  = file_get_contents($request->file('foto_ktp')->getRealPath());
                $newBase64 = 'data:' . $mime . ';base64,' . base64_encode($bin);
            }
            // b) jika FE kirim base64 langsung
            elseif (!empty($data['foto_ktp_base64'])) {
                $raw = $data['foto_ktp_base64'];
                $newBase64 = \Illuminate\Support\Str::startsWith($raw, 'data:')
                    ? $raw
                    : ('data:image/png;base64,' . $raw);
            }

            // hanya set kalau ada yang baru; kalau tidak ada, jangan timpa yg lama
            if (!is_null($newBase64)) {
                $payload['foto_ktp_base64'] = $newBase64;
            } else {
                unset($payload['foto_ktp_base64']);
            }

            // 4) update detail
            $user_detail->update($payload);
        });

        return redirect()->route('dashboard.customer')->with('status', 'Detail user berhasil diperbarui.');
    }



    public function destroy(UserDetail $user_detail)
    {
        $user_detail->delete();
        return back()->with('status', 'Detail user dihapus.');
    }
}
