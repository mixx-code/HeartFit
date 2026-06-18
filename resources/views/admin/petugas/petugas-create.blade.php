@extends('layouts.app')

@section('title', 'Add Petugas/Admin')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add Data Petugas/Admin</h5>
        <small class="text-muted float-end">Lengkapi data di bawah ini</small>
      </div>

      <div class="card-body">
        <form id="petugasForm" method="POST" action="{{ route('admin.data.petugas.store') }}">
          @csrf

          {{-- Nama --}}
          <div class="mb-3">
            <label class="form-label" for="nama">Nama</label>
            <div class="input-group input-group-merge">
              <span id="icon-nama" class="input-group-text"><i class="bx bx-user"></i></span>
              <input type="text" id="nama" name="nama"
                     class="form-control @error('nama') is-invalid @enderror"
                     placeholder="Nama lengkap" aria-describedby="icon-nama"
                     value="{{ old('nama') }}" required>
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
                     value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Password --}}
          <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-lock"></i></span>
              <input type="password" id="password" name="password"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="Minimal 6 karakter" aria-describedby="icon-password"
                     required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Konfirmasi Password --}}
          <div class="mb-3">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-lock"></i></span>
              <input type="password" id="password_confirmation" name="password_confirmation"
                     class="form-control @error('password_confirmation') is-invalid @enderror"
                     placeholder="Ulangi password" aria-describedby="icon-password_confirmation"
                     required>
              @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Nomor HP --}}
          <div class="mb-3">
            <label class="form-label" for="hp">Nomor HP</label>
            <div class="input-group input-group-merge">
              <span id="icon-hp" class="input-group-text"><i class="bx bx-phone"></i></span>
              <input type="text" id="hp" name="hp"
                     class="form-control @error('hp') is-invalid @enderror"
                     placeholder="08xxxx" aria-describedby="icon-hp"
                     value="{{ old('hp') }}">
              @error('hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                
                {{-- Superadmin bisa membuat semua role kecuali superadmin --}}
                @if(auth()->user()->role === 'superadmin')
                  <option value="admin" {{ old('role')==='admin'?'selected':'' }}>Admin</option>
                  <option value="ahli_gizi" {{ old('role')==='ahli_gizi'?'selected':'' }}>Ahli Gizi</option>
                @else
                  {{-- Admin hanya bisa membuat role tertentu --}}
                  <option value="ahli_gizi" {{ old('role')==='ahli_gizi'?'selected':'' }}>Ahli Gizi</option>
                @endif
              </select>
              @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
