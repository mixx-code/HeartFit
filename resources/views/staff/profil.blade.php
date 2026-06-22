@extends('layouts.app')

@section('title', 'Data Saya')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="col-xl-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Saya</h5>
                <small class="text-muted">Perbarui data akun Anda</small>
            </div>

            {{-- Avatar --}}
            <div class="card-body pb-0">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar-initial rounded-circle bg-label-primary"
                          style="width:56px;height:56px;font-size:26px;font-weight:700;display:flex;align-items:center;justify-content:center;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div>
                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                        <small class="text-muted text-capitalize">{{ auth()->user()->role }}</small>
                    </div>
                </div>
            </div>

            <hr class="my-3" />

            <div class="card-body pt-0">

                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show mb-3">
                        <i class="bx bx-check-circle me-1"></i>{{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @php
                    $tgl = old('tanggal_lahir',
                        $user_detail?->tanggal_lahir
                            ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->toDateString()
                            : null
                    );
                    $bb = $tb = '';
                    if (!empty($user_detail?->bb_tb)) {
                        [$bb, $tb] = array_pad(explode('/', $user_detail->bb_tb, 2), 2, '');
                    }
                    $bb = old('berat_badan', trim($bb));
                    $tb = old('tinggi_badan', trim($tb));
                @endphp

                <form id="formStaffEdit" method="POST" action="{{ route('staff.profil.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- === Akun === --}}
                    <h6 class="text-muted text-uppercase small mb-3 mt-2">Akun Login</h6>

                    <div class="mb-3">
                        <label class="form-label" for="name">Nama</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-shield-quarter"></i></span>
                            <input type="text" class="form-control bg-light"
                                value="{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}" readonly>
                        </div>
                    </div>

                    <hr class="my-4" />
                    {{-- === Data Pribadi === --}}
                    <h6 class="text-muted text-uppercase small mb-3">Data Pribadi</h6>

                    <div class="mb-3">
                        <label class="form-label" for="nik">NIK</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                            <input type="text" id="nik" name="nik"
                                class="form-control @error('nik') is-invalid @enderror"
                                value="{{ old('nik', $user_detail?->nik) }}" placeholder="16 digit NIK">
                            @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="alamat">Alamat</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-home"></i></span>
                            <textarea id="alamat" name="alamat"
                                class="form-control @error('alamat') is-invalid @enderror"
                                rows="2" placeholder="Alamat lengkap">{{ old('alamat', $user_detail?->alamat) }}</textarea>
                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-male-female"></i></span>
                            <select id="jenis_kelamin" name="jenis_kelamin"
                                class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="L" @selected(old('jenis_kelamin', $user_detail?->jenis_kelamin) === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('jenis_kelamin', $user_detail?->jenis_kelamin) === 'P')>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tempat &amp; Tanggal Lahir</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <input type="text" name="tempat_lahir"
                                class="form-control @error('tempat_lahir') is-invalid @enderror"
                                placeholder="Tempat lahir"
                                value="{{ old('tempat_lahir', $user_detail?->tempat_lahir) }}">
                            <input type="date" name="tanggal_lahir"
                                class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                value="{{ $tgl }}">
                        </div>
                        @error('tempat_lahir')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @error('tanggal_lahir')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Berat / Tinggi Badan</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-body"></i></span>
                            <input type="number" id="berat_badan" class="form-control"
                                placeholder="Berat (kg)" value="{{ $bb }}">
                            <input type="number" id="tinggi_badan" class="form-control"
                                placeholder="Tinggi (cm)" value="{{ $tb }}">
                        </div>
                        <input type="hidden" id="bb_tb" name="bb_tb" value="{{ old('bb_tb', $user_detail?->bb_tb) }}">
                        <div class="form-text">Format <code>BB/TB</code>, contoh: <code>70/175</code>.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="hp">Nomor HP</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-phone"></i></span>
                            <input type="text" id="hp" name="hp"
                                class="form-control @error('hp') is-invalid @enderror"
                                value="{{ old('hp', $user_detail?->hp) }}" placeholder="08xx...">
                            @error('hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="usia">Usia</label>
                        <input type="number" id="usia" name="usia"
                            class="form-control @error('usia') is-invalid @enderror"
                            placeholder="Tahun" value="{{ old('usia', $user_detail?->usia) }}">
                        @error('usia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('formStaffEdit')?.addEventListener('submit', function () {
        const bb = document.getElementById('berat_badan')?.value?.trim();
        const tb = document.getElementById('tinggi_badan')?.value?.trim();
        document.getElementById('bb_tb').value = (bb || tb) ? `${bb}/${tb}` : '';
    });
</script>
@endpush
