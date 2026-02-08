@extends('layouts.app')

@section('title', 'Detail Petugas/Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Petugas/Admin</h5>
                    <small class="text-muted float-end">Informasi akun petugas</small>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Informasi Akun</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <p class="form-control-plaintext">{{ $user->name }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <p class="form-control-plaintext">{{ $user->email }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-label-primary">{{ ucfirst($user->role) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mb-3">Informasi Sistem</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">ID User</label>
                                <p class="form-control-plaintext">{{ $user->id }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Dibuat Oleh</label>
                                <p class="form-control-plaintext">{{ $user->created_by ?? 'System' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Dibuat</label>
                                <p class="form-control-plaintext">{{ $user->created_at->format('d M Y H:i') }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Terakhir Diupdate</label>
                                <p class="form-control-plaintext">{{ $user->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('admin.data.petugas') }}" class="btn btn-secondary me-2">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <a href="#" class="btn btn-warning">
                            <i class="bx bx-edit"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


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
