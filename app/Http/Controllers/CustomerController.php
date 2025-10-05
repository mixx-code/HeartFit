<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q'));
        $perPage = (int) $request->input('per_page', 10);

        $customers = User::query()
            ->where('role', 'customer')
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

        return view('admin.customers.customers', [
            'customers' => $customers,
            'perPage'   => $perPage,
        ]);
    }


    public function destroy($id, Request $request)
    {
        
    }

    /** FORM CREATE */
    public function create(Request $request)
    {
        return view('admin.customers.customers-create'); // view baru
    }

    /** SIMPAN DATA BARU (dummy ke session) */
    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'nama'          => 'required|string|max:255',
        //     'nik'           => 'required|string|max:30',
        //     'alamat'        => 'required|string',
        //     'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        //     'tempat_lahir'  => 'required|string|max:100',
        //     'tanggal_lahir' => 'required|date',
        //     'bb_tb'         => 'nullable|string|max:50',
        //     'email'         => 'required|email',
        //     'hp'            => 'required|string|max:20',
        // ]);

        // // gabung ttl
        // $validated['ttl'] = $validated['tempat_lahir'] . ', ' . \Carbon\Carbon::parse($validated['tanggal_lahir'])->translatedFormat('d F Y');

        // unset($validated['tempat_lahir'], $validated['tanggal_lahir']);

        // // ambil data lama
        // $customers = session('customers_dummy', []);
        // $newId     = count($customers) ? max(array_column($customers, 'id')) + 1 : 1;

        // $validated['id'] = $newId;

        // $customers[] = $validated;
        // session(['customers_dummy' => $customers]);

        // return redirect()->route('customers.index')->with('status', 'Customer berhasil ditambahkan');
    }


    /** FORM EDIT */
    public function edit($id, Request $request)
    {
        // $data = session('customers_dummy', []);
        // $found = collect($data)->firstWhere('id', (int)$id);

        // if (!$found) {
        //     abort(404, 'Customer tidak ditemukan');
        // }

        // return view('customers-edit', ['customer' => $found]); // view baru
    }

    /** UPDATE DATA */
    public function update($id, Request $request)
    {
        // $this->seedDummyIfNeeded();

        // $data = session('customers_dummy', []);

        // // Validasi sama seperti store
        // $validated = $request->validate([
        //     'nama'           => ['required', 'string', 'max:100'],
        //     'nik'            => ['required', 'string', 'max:20'],
        //     'alamat'         => ['required', 'string', 'max:255'],
        //     'jenis_kelamin'  => ['required', 'in:Laki-laki,Perempuan'],
        //     'ttl'            => ['required', 'string', 'max:100'],
        //     'bb_tb'          => ['nullable', 'string', 'max:50'],
        //     'email'          => ['required', 'email', 'max:100'],
        //     'hp'             => ['required', 'string', 'max:20'],
        // ]);

        // // Update di array
        // foreach ($data as &$row) {
        //     if ((string)$row['id'] === (string)$id) {
        //         $row = array_merge($row, $validated); // id tetap
        //         break;
        //     }
        // }
        // unset($row);

        // session(['customers_dummy' => $data]);

        // // Pertahankan query (opsional)
        // return redirect()
        //     ->route('customers.index', $request->only('q', 'per_page', 'page'))
        //     ->with('status', 'Data customer diperbarui.');
    }
}
