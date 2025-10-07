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

          {{-- NIK --}}
          <div class="mb-3">
            <label class="form-label" for="nik">NIK</label>
            <div class="input-group input-group-merge">
              <span id="icon-nik" class="input-group-text"><i class="bx bx-id-card"></i></span>
              <input type="text" id="nik" name="nik"
                     class="form-control @error('nik') is-invalid @enderror"
                     placeholder="Nomor Induk Kependudukan" aria-describedby="icon-nik"
                     value="{{ old('nik') }}" required>
              @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Alamat --}}
          <div class="mb-3">
            <label class="form-label" for="alamat">Alamat</label>
            <div class="input-group input-group-merge">
              <span id="icon-alamat" class="input-group-text"><i class="bx bx-home"></i></span>
              <textarea id="alamat" name="alamat"
                        class="form-control @error('alamat') is-invalid @enderror"
                        placeholder="Alamat lengkap"
                        aria-describedby="icon-alamat" required>{{ old('alamat') }}</textarea>
              @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Jenis Kelamin --}}
          <div class="mb-3">
            <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
            <div class="input-group input-group-merge">
              <span id="icon-jk" class="input-group-text"><i class="bx bx-male-female"></i></span>
              <select id="jenis_kelamin" name="jenis_kelamin"
                      class="form-select @error('jenis_kelamin') is-invalid @enderror"
                      aria-describedby="icon-jk" required>
                <option value="">-- Pilih --</option>
                <option value="L" {{ old('jenis_kelamin')==='L'?'selected':'' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin')==='P'?'selected':'' }}>Perempuan</option>
              </select>
              @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- Tempat & Tanggal Lahir --}}
          <div class="mb-3">
            <label class="form-label">Tempat & Tanggal Lahir</label>
            <div class="input-group">
              <span id="icon-ttl" class="input-group-text"><i class="bx bx-calendar"></i></span>
              <input type="text" id="tempat_lahir" name="tempat_lahir"
                     class="form-control @error('tempat_lahir') is-invalid @enderror"
                     placeholder="Tempat lahir" value="{{ old('tempat_lahir') }}" required>
              <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                     class="form-control @error('tanggal_lahir') is-invalid @enderror"
                     value="{{ old('tanggal_lahir') }}" required>
            </div>
            @error('tempat_lahir')<div class="text-danger small">{{ $message }}</div>@enderror
            @error('tanggal_lahir')<div class="text-danger small">{{ $message }}</div>@enderror
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
            <div class="form-text">Password default bisa di-set di controller (misal: <code>password123!</code>).</div>
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
                <option value="admin"           {{ old('role')==='admin'?'selected':'' }}>Admin</option>
                <option value="ahli_gizi"       {{ old('role')==='ahli_gizi'?'selected':'' }}>Ahli Gizi</option>
                <option value="medical_record"  {{ old('role')==='medical_record'?'selected':'' }}>Medical Record</option>
                <option value="bendahara"       {{ old('role')==='bendahara'?'selected':'' }}>Bendahara</option>
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
