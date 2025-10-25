@extends('layouts.app')

@section('title', 'Detail Akun')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Akun</h5>
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
                        // Format tanggal_lahir → YYYY-MM-DD agar <input type="date"> bisa prefill
                        $tgl = old(
                            'tanggal_lahir',
                            $detail->tanggal_lahir
                                ? ($detail->tanggal_lahir instanceof \Carbon\Carbon
                                    ? $detail->tanggal_lahir->toDateString()
                                    : \Illuminate\Support\Carbon::parse($detail->tanggal_lahir)->toDateString())
                                : null,
                        );

                        // Pecah bb_tb "54/167" → $bb=54, $tb=167 untuk prefill input angka
                        $bb = $tb = '';
                        if (!empty($detail->bb_tb)) {
                            [$bb, $tb] = array_pad(explode('/', $detail->bb_tb, 2), 2, '');
                            $bb = trim((string) $bb);
                            $tb = trim((string) $tb);
                        }
                        $bb = old('berat_badan', $bb);
                        $tb = old('tinggi_badan', $tb);

                        // Siapkan sumber gambar KTP (untuk bagian bawah)
                        $srcKtp = null;
                        if (!empty($fotoKtp)) {
                            $srcKtp = \Illuminate\Support\Str::startsWith($fotoKtp, 'data:')
                                ? $fotoKtp
                                : 'data:image/png;base64,' . $fotoKtp;
                        }
                    @endphp

                    <form id="formCustomerEdit" method="POST"
                        action="{{ route('customer.akun.update', $detail->id) }}" enctype="multipart/form-data">
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

                        <div class="mb-3">
                            <label class="form-label" for="role">Role</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-shield-quarter"></i></span>
                                <input type="text" id="role" name="role"
                                    class="form-control @error('role') is-invalid @enderror"
                                    value="{{ old('role', $detail->user->role) }}" readonly>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- === Data Detail === --}}
                        <div class="mb-3">
                            <label class="form-label" for="mr">Medical Record (MR)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                                <input type="text" id="mr" name="mr"
                                    class="form-control @error('mr') is-invalid @enderror"
                                    value="{{ old('mr', $detail->mr) }}">
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
                                <input type="number" id="berat_badan" class="form-control" placeholder="Berat (kg)"
                                    value="{{ $bb }}">
                                <input type="number" id="tinggi_badan" class="form-control" placeholder="Tinggi (cm)"
                                    value="{{ $tb }}">
                            </div>
                            {{-- field yang dipost ke server (sesuai validator) --}}
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
                                value="{{ old('usia', $detail->usia) }}">
                            @error('usia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ================== Preview KTP + Upload (DIPINDAH KE BAWAH) ================== --}}
                        <hr class="my-4" />
                        <h6 class="mb-3">Foto KTP</h6>
                        <div class="d-flex flex-column align-items-center mb-4 text-center">
                            {{-- Gambar KTP --}}
                            <img src="{{ $srcKtp ?? asset('assets/img/placeholder-id.png') }}" alt="Foto KTP"
                                class="rounded border mb-3" height="200" width="320" id="previewKtp" />

                            {{-- Tombol upload & reset --}}
                            <div class="button-wrapper">
                                <label for="uploadKtp" class="btn btn-primary me-2 mb-2" tabindex="0">
                                    <span class="d-none d-sm-inline">Upload KTP baru</span>
                                    <i class="bx bx-upload d-inline d-sm-none"></i>
                                    <input type="file" id="uploadKtp" class="account-file-input" hidden
                                        accept="image/png,image/jpeg" name="foto_ktp" form="formCustomerEdit" />
                                </label>
                                <button type="button" class="btn btn-outline-secondary mb-2" id="resetKtpBtn">
                                    <i class="bx bx-reset d-inline d-sm-none"></i>
                                    <span class="d-none d-sm-inline">Reset</span>
                                </button>
                                <p class="text-muted mb-0">Allowed JPG/PNG. Max 800KB</p>
                            </div>
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
        // Preview/reset KTP (tetap berfungsi meski dipindah ke bawah)
        const upload = document.getElementById('uploadKtp');
        const preview = document.getElementById('previewKtp');
        const resetBtn = document.getElementById('resetKtpBtn');
        const originalSrc = preview ? preview.src : null;

        if (upload && preview) {
            upload.addEventListener('change', (e) => {
                const f = e.target.files?.[0];
                if (!f) return;
                const reader = new FileReader();
                reader.onload = () => preview.src = reader.result;
                reader.readAsDataURL(f);
            });
        }
        if (resetBtn && preview && originalSrc) {
            resetBtn.addEventListener('click', () => {
                preview.src = originalSrc;
                if (upload) upload.value = '';
            });
        }

        // Gabungkan BB/TB saat submit
        document.getElementById('formCustomerEdit')?.addEventListener('submit', function() {
            const bb = document.getElementById('berat_badan')?.value?.trim();
            const tb = document.getElementById('tinggi_badan')?.value?.trim();
            const hidden = document.getElementById('bb_tb');
            hidden.value = (bb && tb) ? `${bb}/${tb}` : (bb || tb ? `${bb}/${tb}` : '');
        });
    </script>
@endpush
