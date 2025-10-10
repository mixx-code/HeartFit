@extends('layouts.app')

@section('title', 'Add Menu Makanan')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add Data Menu Makanan</h5>
                    <small class="text-muted float-end">Lengkapi data berikut</small>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.menuMakanan.store') }}">
                        @csrf

                        {{-- Nama Menu --}}
                        <div class="mb-3">
                            <label class="form-label" for="menu_number">
                                <i class="bx bx-list-ol"></i>Buat Menu
                            </label>
                            {{-- FIX: JANGAN ada name di sini --}}
                            <select id="menu_number" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Menu --</option>
                                @for ($i = 1; $i <= 11; $i++)
                                    <option value="{{ $i }}">Menu {{ $i }}</option>
                                @endfor
                            </select>
                            <small class="text-muted d-block mt-1">
                                Memilih menu akan mengisi <code>nama_menu</code> dan <code>serve_days</code> otomatis.
                            </small>
                        </div>

                        {{-- Batch --}}
                        <div class="mb-3">
                            <label class="form-label" for="batch">Batch</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-tag"></i></span>
                                <input type="text" id="batch" name="batch" class="form-control"
                                    placeholder="Contoh: II" required>
                            </div>
                            <small class="text-muted">Contoh: I, II, III</small>
                        </div>

                        {{-- Preview Serve Days --}}
                        <div class="mb-3">
                            <label class="form-label">Tanggal Menu Disajikan (Preview)</label>
                            <input id="serve_days_preview" name="" type="text" class="form-control" readonly
                                placeholder="Belum dipilih">
                        </div>

                        {{-- Hidden: serve_days (JSON string) --}}
                        <input type="hidden" id="nama_menu" name="nama_menu" value="">
                        <input type="hidden" id="serve_days" name="serve_days" value="">

                        {{-- Makan Siang --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-1">
                                <i class="bx bx-basket"></i> Makan Siang
                            </label>
                            <div id="makanSiangContainer">
                                <div class="input-group mb-2">
                                    <input type="text" name="makan_siang[]" class="form-control"
                                        placeholder="Contoh: Nasi Merah">
                                    <button type="button" class="btn btn-outline-primary add-makan-siang">
                                        <i class="bx bx-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Makan Malam --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-1">
                                <i class="bx bx-bowl-rice"></i> Makan Malam
                            </label>
                            <div id="makanMalamContainer">
                                <div class="input-group mb-2">
                                    <input type="text" name="makan_malam[]" class="form-control"
                                        placeholder="Contoh: Potato Wedges (Panggang)">
                                    <button type="button" class="btn btn-outline-primary add-makan-malam">
                                        <i class="bx bx-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // ====== Mapping Serve Days ======
            const serveDaysFor = (n) => {
                n = parseInt(n, 10);
                if (!n || n < 1 || n > 11) return [];
                if (n === 11) return [31];
                return [n, n + 10, n + 20];
            };

            const menuNumber = document.getElementById('menu_number');
            const serveDaysInput = document.getElementById('serve_days');
            const serveDaysPreview = document.getElementById('serve_days_preview');
            const namaMenu = document.getElementById('nama_menu');

            menuNumber.addEventListener('change', () => {
                const n = parseInt(menuNumber.value, 10);
                const days = serveDaysFor(n);
                serveDaysInput.value = JSON.stringify(days);
                serveDaysPreview.value = days.length ? days.join(', ') : 'Belum dipilih';
                namaMenu.value = `Menu ${n}`; // pastikan hidden ini terisi
            });


            // ====== Dinamis Makan Siang ======
            const makanSiangContainer = document.getElementById("makanSiangContainer");
            makanSiangContainer.addEventListener("click", function(e) {
                if (e.target.closest(".add-makan-siang")) {
                    const row = document.createElement("div");
                    row.className = "input-group mb-2";
                    row.innerHTML = `
        <input type="text" name="makan_siang[]" class="form-control" placeholder="Masukkan item Makan Siang">
        <button type="button" class="btn btn-outline-danger remove-input"><i class="bx bx-minus"></i></button>
      `;
                    makanSiangContainer.appendChild(row);
                }
                if (e.target.closest(".remove-input")) {
                    e.target.closest(".input-group").remove();
                }
            });

            // ====== Dinamis Makan Malam ======
            const makanMalamContainer = document.getElementById("makanMalamContainer");
            makanMalamContainer.addEventListener("click", function(e) {
                if (e.target.closest(".add-makan-malam")) {
                    const row = document.createElement("div");
                    row.className = "input-group mb-2";
                    row.innerHTML = `
        <input type="text" name="makan_malam[]" class="form-control" placeholder="Masukkan item Makan Malam">
        <button type="button" class="btn btn-outline-danger remove-input"><i class="bx bx-minus"></i></button>
      `;
                    makanMalamContainer.appendChild(row);
                }
                if (e.target.closest(".remove-input")) {
                    e.target.closest(".input-group").remove();
                }
            });
        });
    </script>
@endpush
