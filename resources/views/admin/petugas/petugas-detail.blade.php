@extends('layouts.app')

@section('title', 'Detail Petugas/Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Data Petugas/Admin</h5>
                    <small class="text-muted float-end">Perbarui data berikut</small>
                </div>

                {{-- ================== FOTO PROFIL (ATAS) ================== --}}
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://placehold.co/400" alt="Foto Profil" class="rounded-circle border object-fit-cover"
                            width="120" height="120" />
                        <div>
                            <h6 class="mb-1">Foto Profil</h6>
                            <p class="text-muted mb-2">Placeholder foto profil. (Jika nanti ada field foto profil, bisa
                                ditambahkan upload di sini.)</p>
                        </div>
                    </div>
                </div>

                <hr class="my-0" />

                {{-- ================== FORM ================== --}}
                <div class="card-body">
                    @php
                        // tgl untuk input date
                        $tgl = old(
                            'tanggal_lahir',
                            $detail->tanggal_lahir
                                ? ($detail->tanggal_lahir instanceof \Carbon\Carbon
                                    ? $detail->tanggal_lahir->toDateString()
                                    : \Illuminate\Support\Carbon::parse($detail->tanggal_lahir)->toDateString())
                                : null,
                        );

                        // pecah BB/TB dari bb_tb
                        $bb = $tb = '';
                        if (!empty($detail->bb_tb)) {
                            [$bb, $tb] = array_pad(explode('/', $detail->bb_tb, 2), 2, '');
                            $bb = trim((string) $bb);
                            $tb = trim((string) $tb);
                        }

                        // gunakan old() agar tetap muncul setelah validation error
                        $bbVal = old('berat_badan', $bb);
                        $tbVal = old('tinggi_badan', $tb);

                        // usia: pakai DB kalau ada; kalau kosong dan ada tanggal_lahir, hitung otomatis
                        $usiaCalc = null;
                        if (empty($detail->usia) && !empty($detail->tanggal_lahir)) {
                            $usiaCalc = \Illuminate\Support\Carbon::parse($detail->tanggal_lahir)->age;
                        }
                        $usiaVal = old('usia', $detail->usia ?? $usiaCalc);
                    @endphp


                    <form id="formPetugasEdit" method="POST"
                        action="{{ route('admin.data.petugas.update', $detail->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- === Data Akun === --}}
                        <div class="mb-3">
                            <label class="form-label" for="name">Nama (Akun)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $detail->user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">Email (Akun)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $detail->user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Role --}}
                        <div class="mb-3">
                            <label class="form-label" for="role">Role</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-shield-quarter"></i></span>
                                <select id="role" name="role"
                                    class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin"
                                        {{ old('role', $detail->user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="ahli_gizi"
                                        {{ old('role', $detail->user->role) === 'ahli_gizi' ? 'selected' : '' }}>Ahli Gizi
                                    </option>
                                    <option value="medical_record"
                                        {{ old('role', $detail->user->role) === 'medical_record' ? 'selected' : '' }}>
                                        Medical
                                        Record</option>
                                    <option value="bendahara"
                                        {{ old('role', $detail->user->role) === 'bendahara' ? 'selected' : '' }}>Bendahara
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- === Data Detail === --}}
                        <div class="mb-3">
                            <label class="form-label" for="mr">Medical Record (MR) <span
                                    class="text-muted">(opsional)</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                                <input type="text" id="mr" name="mr"
                                    class="form-control @error('mr') is-invalid @enderror"
                                    placeholder="Biarkan kosong jika tidak dipakai" value="{{ old('mr', $detail->mr) }}">
                                @error('mr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="nik">NIK</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                                <input type="text" id="nik" name="nik"
                                    class="form-control @error('nik') is-invalid @enderror"
                                    value="{{ old('nik', $detail->nik) }}">
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="alamat">Alamat</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-home"></i></span>
                                <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2"
                                    placeholder="Alamat lengkap">{{ old('alamat', $detail->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-male-female"></i></span>
                                <select id="jenis_kelamin" name="jenis_kelamin"
                                    class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" @selected(old('jenis_kelamin', $detail->jenis_kelamin) === 'L')>Laki-laki</option>
                                    <option value="P" @selected(old('jenis_kelamin', $detail->jenis_kelamin) === 'P')>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tempat & Tanggal Lahir</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" id="tempat_lahir" name="tempat_lahir"
                                    class="form-control @error('tempat_lahir') is-invalid @enderror"
                                    placeholder="Tempat lahir" value="{{ old('tempat_lahir', $detail->tempat_lahir) }}">
                                <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                    class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                    value="{{ $tgl }}">
                            </div>
                            @error('tempat_lahir')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('tanggal_lahir')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Berat Badan / Tinggi Badan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-body"></i></span>
                                {{-- tambahkan name supaya old() berfungsi --}}
                                <input type="number" id="berat_badan" name="berat_badan" class="form-control"
                                    placeholder="Berat (kg)" value="{{ $bbVal }}">
                                <input type="number" id="tinggi_badan" name="tinggi_badan" class="form-control"
                                    placeholder="Tinggi (cm)" value="{{ $tbVal }}">
                            </div>
                            {{-- field yang dipost ke server --}}
                            <input type="hidden" id="bb_tb" name="bb_tb"
                                value="{{ old('bb_tb', $detail->bb_tb) }}">
                            <div class="form-text">Otomatis digabung format <code>BB/TB</code>, contoh:
                                <code>60/170</code>.</div>
                            @error('bb_tb')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="hp">Nomor HP</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                <input type="text" id="hp" name="hp"
                                    class="form-control @error('hp') is-invalid @enderror"
                                    value="{{ old('hp', $detail->hp) }}">
                                @error('hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="usia">Usia</label>
                            <input type="number" id="usia" name="usia"
                                class="form-control @error('usia') is-invalid @enderror" placeholder="Usia (tahun)"
                                value="{{ $usiaVal }}">
                            @error('usia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Pertahankan kondisi list saat kembali --}}
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        <input type="hidden" name="page" value="{{ request('page') }}">

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary me-2">Simpan</button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Gabungkan BB/TB saat submit (ID form yang benar)
        document.getElementById('formPetugasEdit')?.addEventListener('submit', function() {
            const bb = document.getElementById('berat_badan')?.value?.trim();
            const tb = document.getElementById('tinggi_badan')?.value?.trim();
            const hidden = document.getElementById('bb_tb');
            hidden.value = (bb && tb) ? `${bb}/${tb}` : (bb || tb ? `${bb}/${tb}` : '');
        });
    </script>
@endpush
