@extends('layouts.app')

@section('title', 'Edit Petugas/Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Data Petugas/Admin</h5>
                    <small class="text-muted float-end">Perbarui data berikut</small>
                </div>

                {{-- Error Messages --}}
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.data.petugas.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label class="form-label" for="nama">Nama</label>
                            <div class="input-group input-group-merge">
                                <span id="icon-nama" class="input-group-text"><i class="bx bx-user"></i></span>
                                <input type="text" id="nama" name="name"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       placeholder="Nama lengkap" aria-describedby="icon-nama"
                                       value="{{ old('nama', $user->name) }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input type="email" id="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="nama@email.com" aria-describedby="icon-email"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Role --}}
                        <div class="mb-3">
                            <label class="form-label" for="role">Role</label>
                            <div class="input-group input-group-merge">
                                <span id="icon-role" class="input-group-text"><i class="bx bx-shield-quarter"></i></span>
                                <select id="role" name="role"
                                        class="form-select @error('role') is-invalid @enderror"
                                        aria-describedby="icon-role" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="ahli_gizi" {{ old('role', $user->role) === 'ahli_gizi' ? 'selected' : '' }}>Ahli Gizi</option>
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.data.petugas') }}" class="btn btn-secondary me-2">
                                <i class="bx bx-arrow-back"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
