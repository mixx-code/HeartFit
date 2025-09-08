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
      <form>
        {{-- Nama --}}
        <div class="mb-3">
          <label class="form-label" for="nama">Nama</label>
          <div class="input-group input-group-merge">
            <span id="icon-nama" class="input-group-text"><i class="bx bx-user"></i></span>
            <input type="text" id="nama" class="form-control" placeholder="Nama lengkap"
                   aria-describedby="icon-nama" />
          </div>
        </div>

        {{-- NIK --}}
        <div class="mb-3">
          <label class="form-label" for="nik">NIK</label>
          <div class="input-group input-group-merge">
            <span id="icon-nik" class="input-group-text"><i class="bx bx-id-card"></i></span>
            <input type="text" id="nik" class="form-control" placeholder="Nomor Induk Kependudukan"
                   aria-describedby="icon-nik" />
          </div>
        </div>

        {{-- Alamat --}}
        <div class="mb-3">
          <label class="form-label" for="alamat">Alamat</label>
          <div class="input-group input-group-merge">
            <span id="icon-alamat" class="input-group-text"><i class="bx bx-home"></i></span>
            <textarea id="alamat" class="form-control" placeholder="Alamat lengkap"
                      aria-describedby="icon-alamat"></textarea>
          </div>
        </div>

        {{-- Jenis Kelamin --}}
        <div class="mb-3">
          <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
          <div class="input-group input-group-merge">
            <span id="icon-jk" class="input-group-text"><i class="bx bx-male-female"></i></span>
            <select id="jenis_kelamin" class="form-select" aria-describedby="icon-jk">
              <option value="">-- Pilih --</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>
        </div>

        {{-- Tempat Tanggal Lahir --}}
        <div class="mb-3">
          <label class="form-label" for="ttl">Tempat Tanggal Lahir</label>
          <div class="input-group">
            <span id="icon-ttl" class="input-group-text"><i class="bx bx-calendar"></i></span>
            <input type="text" id="tempat" class="form-control" placeholder="Tempat lahir" />
            <input type="date" id="tanggal_lahir" class="form-control" />
          </div>
        </div>

        {{-- Posisi --}}
        <div class="mb-3">
          <label class="form-label" for="posisi">Posisi</label>
          <div class="input-group input-group-merge">
            <span id="icon-posisi" class="input-group-text"><i class="bx bx-briefcase"></i></span>
            <input type="text" id="posisi" class="form-control" placeholder="Posisi / Jabatan"
                   aria-describedby="icon-posisi" />
          </div>
        </div>

        {{-- Email --}}
        <div class="mb-3">
          <label class="form-label" for="email">Email</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
            <input type="email" id="email" class="form-control" placeholder="nama@email.com"
                   aria-describedby="icon-email" />
          </div>
        </div>

        {{-- Nomor HP --}}
        <div class="mb-3">
          <label class="form-label" for="hp">Nomor HP</label>
          <div class="input-group input-group-merge">
            <span id="icon-hp" class="input-group-text"><i class="bx bx-phone"></i></span>
            <input type="text" id="hp" class="form-control" placeholder="08xxxx"
                   aria-describedby="icon-hp" />
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
