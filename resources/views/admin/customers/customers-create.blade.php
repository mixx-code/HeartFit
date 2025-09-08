@extends('layouts.app')

@section('title', 'Add Customer')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add Data Customer</h5>
                    <small class="text-muted float-end">Lengkapi data berikut</small>
                </div>
                <div class="card-body">
                    <form>
                        {{-- Nama --}}
                        <div class="mb-3">
                            <label class="form-label" for="nama">Nama</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                <input type="text" id="nama" name="nama" class="form-control"
                                    placeholder="Nama lengkap">
                            </div>
                        </div>

                        {{-- NIK --}}
                        <div class="mb-3">
                            <label class="form-label" for="nik">NIK</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                                <input type="text" id="nik" name="nik" class="form-control"
                                    placeholder="Nomor Induk Kependudukan">
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label class="form-label" for="alamat">Alamat</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-home"></i></span>
                                <textarea id="alamat" name="alamat" class="form-control" placeholder="Alamat lengkap"></textarea>
                            </div>
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="mb-3">
                            <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-male-female"></i></span>
                                <select id="jenis_kelamin" name="jenis_kelamin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        {{-- Tempat Tanggal Lahir --}}
                        <div class="mb-3">
                            <label class="form-label">Tempat Tanggal Lahir</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" id="tempat_lahir" name="tempat_lahir"
                                    class="form-control @error('tempat_lahir') is-invalid @enderror"
                                    placeholder="Tempat lahir" value="{{ old('tempat_lahir') }}" required>
                                <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                    class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                    value="{{ old('tanggal_lahir') }}" required>
                                @error('tempat_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Berat / Tinggi Badan --}}
                        <div class="mb-3">
                            <label class="form-label">Berat Badan / Tinggi Badan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-body"></i></span>
                                <input type="number" id="berat_badan" name="berat_badan" class="form-control"
                                    placeholder="Berat (kg)">
                                <input type="number" id="tinggi_badan" name="tinggi_badan" class="form-control"
                                    placeholder="Tinggi (cm)">
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="nama@email.com">
                            </div>
                        </div>

                        {{-- Nomor HP --}}
                        <div class="mb-3">
                            <label class="form-label" for="hp">Nomor HP</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                <input type="text" id="hp" name="hp" class="form-control"
                                    placeholder="08xxxx">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection
