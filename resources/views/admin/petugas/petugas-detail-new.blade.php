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
                                <p class="form-control-plaintext">{{ $user->created_at ? $user->created_at->format('d M Y H:i') : 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Terakhir Diupdate</label>
                                <p class="form-control-plaintext">{{ $user->updated_at ? $user->updated_at->format('d M Y H:i') : 'N/A' }}</p>
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
