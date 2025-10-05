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
        <form id="customerForm" method="POST" action="{{ route('admin.data.customers.create') }}">
          @csrf

          {{-- === Data Akun (harus ada, sesuai store()) === --}}
          <div class="mb-3">
            <label class="form-label" for="name">Nama (Akun)</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-user"></i></span>
              <input type="text" id="name" name="name"
                     class="form-control @error('name') is-invalid @enderror"
                     placeholder="Nama lengkap" value="{{ old('name') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="email">Email (Akun)</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-envelope"></i></span>
              <input type="email" id="email" name="email"
                     class="form-control @error('email') is-invalid @enderror"
                     placeholder="nama@email.com" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-text">Password default: <code>password123!</code> (bisa diubah nanti).</div>
          </div>

          {{-- === Data Detail === --}}
          <div class="mb-3">
            <label class="form-label" for="mr">Medical Record (MR)</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-id-card"></i></span>
              <input type="text" id="mr" name="mr"
                     class="form-control @error('mr') is-invalid @enderror"
                     placeholder="Misal: MR-000001" value="{{ old('mr') }}" required>
              @error('mr')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="nik">NIK</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-id-card"></i></span>
              <input type="text" id="nik" name="nik"
                     class="form-control @error('nik') is-invalid @enderror"
                     placeholder="Nomor Induk Kependudukan" value="{{ old('nik') }}" required>
              @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="alamat">Alamat</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-home"></i></span>
              <textarea id="alamat" name="alamat"
                        class="form-control @error('alamat') is-invalid @enderror"
                        placeholder="Alamat lengkap" required>{{ old('alamat') }}</textarea>
              @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-male-female"></i></span>
              <select id="jenis_kelamin" name="jenis_kelamin"
                      class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                <option value="">-- Pilih --</option>
                <option value="L" {{ old('jenis_kelamin')==='L'?'selected':'' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin')==='P'?'selected':'' }}>Perempuan</option>
              </select>
              @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Tempat & Tanggal Lahir</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bx bx-calendar"></i></span>
              <input type="text" id="tempat_lahir" name="tempat_lahir"
                     class="form-control @error('tempat_lahir') is-invalid @enderror"
                     placeholder="Tempat lahir" value="{{ old('tempat_lahir') }}" required>
              <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                     class="form-control @error('tanggal_lahir') is-invalid @enderror"
                     value="{{ old('tanggal_lahir') }}" required>
              @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
              @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Berat Badan / Tinggi Badan</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bx bx-body"></i></span>
              <input type="number" id="berat_badan" class="form-control" placeholder="Berat (kg)" value="{{ old('berat_badan') }}">
              <input type="number" id="tinggi_badan" class="form-control" placeholder="Tinggi (cm)" value="{{ old('tinggi_badan') }}">
            </div>
            {{-- field yang dipost ke server (sesuai validator) --}}
            <input type="hidden" id="bb_tb" name="bb_tb" value="{{ old('bb_tb') }}">
            <div class="form-text">Otomatis digabung menjadi format <code>BB/TB</code>, contoh: <code>60/170</code>.</div>
            @error('bb_tb')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="hp">Nomor HP</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-phone"></i></span>
              <input type="text" id="hp" name="hp"
                     class="form-control @error('hp') is-invalid @enderror"
                     placeholder="08xxxx" value="{{ old('hp') }}">
              @error('hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="usia">Usia</label>
            <input type="number" id="usia" name="usia"
                   class="form-control @error('usia') is-invalid @enderror"
                   placeholder="Usia (tahun)" value="{{ old('usia') }}">
            @error('usia')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="formFile">Foto KTP</label>
            <input class="form-control @error('foto_ktp_base64') is-invalid @enderror" type="file" id="formFile" accept="image/*">
            {{-- field yang dipost ke server (sesuai validator) --}}
            <input type="hidden" name="foto_ktp_base64" id="foto_ktp_base64" value="{{ old('foto_ktp_base64') }}">
            @error('foto_ktp_base64')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Gambar akan dikonversi ke Base64 sebelum dikirim.</div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Gabungkan berat/tinggi → bb_tb saat submit
  document.getElementById('customerForm').addEventListener('submit', function(e) {
    const bb = document.getElementById('berat_badan').value?.trim();
    const tb = document.getElementById('tinggi_badan').value?.trim();
    const hidden = document.getElementById('bb_tb');
    hidden.value = (bb && tb) ? `${bb}/${tb}` : (bb || tb ? `${bb}/${tb}` : '');
  });

  // Convert file → base64 ke hidden input
  const fileInput = document.getElementById('formFile');
  const hiddenBase64 = document.getElementById('foto_ktp_base64');

  fileInput?.addEventListener('change', () => {
    const file = fileInput.files?.[0];
    if (!file) { hiddenBase64.value = ''; return; }

    const reader = new FileReader();
    reader.onload = () => {
      // hasil: data:image/png;base64,xxxxx
      hiddenBase64.value = reader.result;
    };
    reader.onerror = () => {
      console.error('Gagal membaca file KTP');
      hiddenBase64.value = '';
    };
    reader.readAsDataURL(file);
  });
</script>
@endpush
@endsection
