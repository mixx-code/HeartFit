@extends('layouts.app')

@section('title', 'Add Paket Makanan')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add Data Paket Makanan</h5>
        <small class="text-muted float-end">Lengkapi data berikut</small>
      </div>

      <div class="card-body">
        <form method="POST" action="#">
          {{-- Nama Menu --}}
          <div class="mb-3">
            <label class="form-label" for="nama_menu">Nama Menu</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-restaurant"></i></span>
              <input
                type="text"
                id="nama_menu"
                name="nama_menu"
                class="form-control"
                placeholder="Contoh: Menu 1"
                required>
            </div>
          </div>

          {{-- Batch --}}
          <div class="mb-3">
            <label class="form-label" for="batch">Batch</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-tag"></i></span>
              <input
                type="text"
                id="batch"
                name="batch"
                class="form-control"
                placeholder="Contoh: II"
                required>
            </div>
            <small class="text-muted">Contoh: I, II, III</small>
          </div>

          {{-- Makan Siang --}}
          <div class="mb-3">
            <label class="form-label d-flex align-items-center gap-1">
              <i class="bx bx-basket"></i> Makan Siang (satu item per baris)
            </label>
            <textarea
              id="makan_siang"
              name="makan_siang"
              class="form-control"
              rows="5"
              placeholder="Nasi Merah&#10;Ayam Suwir Daun Kemangi&#10;Oseng Tempe Cabe Ijo&#10;Tumis Buncis Putren&#10;Buah Apel"></textarea>
          </div>

          {{-- Makan Malam --}}
          <div class="mb-3">
            <label class="form-label d-flex align-items-center gap-1">
              <i class="bx bx-bowl-rice"></i> Makan Malam (satu item per baris)
            </label>
            <textarea
              id="makan_malam"
              name="makan_malam"
              class="form-control"
              rows="5"
              placeholder="Potato Wedges (Panggang)&#10;Grilled Beef Brown Sauce&#10;Vegetable Salad&#10;Buah Jeruk"></textarea>
          </div>

          {{-- JSON Mentah --}}
          <div class="mb-3">
            <details>
              <summary class="mb-2">Atau masukkan <strong>JSON mentah</strong> ke kolom di bawah (opsional)</summary>
              <label class="form-label" for="spec_menu_raw">spec_menu (JSON)</label>
              <textarea
                id="spec_menu_raw"
                name="spec_menu_raw"
                class="form-control"
                rows="6"
                placeholder='{"Makan Siang":["..."],"Makan Malam":["..."]}'></textarea>
              <small class="text-muted">Jika ini diisi, sistem bisa langsung menyimpan nilai JSON ini.</small>
            </details>
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
