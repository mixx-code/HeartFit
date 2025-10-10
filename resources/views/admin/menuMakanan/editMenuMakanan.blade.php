@extends('layouts.app')

@section('title', 'Edit Menu Makanan')
@section('content')
    @php
        // Ambil angka menu dari "Menu X"
        $initialNumber = null;
        if (!empty($menuMakanan->nama_menu) && preg_match('/Menu\s+(\d+)/', $menuMakanan->nama_menu, $m)) {
            $initialNumber = (int) $m[1];
        }

        // Berkat casts di model, keduanya SUDAH array:
        // #casts = ['serve_days' => 'array', 'spec_menu' => 'array']
        $serveDays = $menuMakanan->serve_days ?? [];

        // spec_menu: {"Makan Malam": [...], "Makan Siang": [...]}
        $spec = $menuMakanan->spec_menu ?? [];
        $makanSiang = data_get($spec, 'Makan Siang', []);
        $makanMalam = data_get($spec, 'Makan Malam', []);

        // Pastikan minimal 1 input tampil
        if (empty($makanSiang)) $makanSiang = [''];
        if (empty($makanMalam)) $makanMalam = [''];
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Data Menu Makanan</h5>
                    <small class="text-muted float-end">Perbarui data berikut</small>
                </div>

                <div class="card-body">
                    <form method="POST" 
                    {{-- action="{{ route('admin.menuMakanan.update', $menuMakanan->id) }}" --}}
                    >
                        @csrf
                        @method('PUT')

                        {{-- Pilih Menu (mengisi hidden nama_menu & serve_days otomatis bila diubah) --}}
                        <div class="mb-3">
                            <label class="form-label" for="menu_number">
                                <i class="bx bx-list-ol"></i> Pilih Menu
                            </label>
                            <select id="menu_number" class="form-select" required>
                                <option value="" disabled {{ $initialNumber ? '' : 'selected' }}>-- Pilih Menu --</option>
                                @for ($i = 1; $i <= 11; $i++)
                                    <option value="{{ $i }}" {{ $initialNumber === $i ? 'selected' : '' }}>
                                        Menu {{ $i }}
                                    </option>
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
                                       placeholder="Contoh: II" value="{{ old('batch', $menuMakanan->batch) }}" required>
                            </div>
                            <small class="text-muted">Contoh: I, II, III</small>
                        </div>

                        {{-- Preview Serve Days --}}
                        <div class="mb-3">
                            <label class="form-label">Tanggal Menu Disajikan (Preview)</label>
                            <input id="serve_days_preview" type="text" class="form-control" readonly
                                   placeholder="Belum dipilih"
                                   value="{{ !empty($serveDays) ? implode(', ', $serveDays) : '' }}">
                        </div>

                        {{-- Hidden: nama_menu & serve_days (JSON string di-submit) --}}
                        <input type="hidden" id="nama_menu" name="nama_menu" value="{{ old('nama_menu', $menuMakanan->nama_menu) }}">
                        <input type="hidden" id="serve_days" name="serve_days" value='@json($serveDays)'>

                        {{-- Makan Siang --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-1">
                                <i class="bx bx-basket"></i> Makan Siang
                            </label>
                            <div id="makanSiangContainer">
                                @foreach ($makanSiang as $idx => $item)
                                    <div class="input-group mb-2">
                                        <input type="text" name="makan_siang[]" class="form-control"
                                               placeholder="Masukkan item Makan Siang"
                                               value="{{ old('makan_siang.' . $idx, $item) }}">
                                        @if ($idx === 0)
                                            <button type="button" class="btn btn-outline-primary add-makan-siang">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-danger remove-input">
                                                <i class="bx bx-minus"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Makan Malam --}}
                        <div class="mb-3">
                            <label class="form-label d-flex align-items-center gap-1">
                                <i class="bx bx-bowl-rice"></i> Makan Malam
                            </label>
                            <div id="makanMalamContainer">
                                @foreach ($makanMalam as $idx => $item)
                                    <div class="input-group mb-2">
                                        <input type="text" name="makan_malam[]" class="form-control"
                                               placeholder="Masukkan item Makan Malam"
                                               value="{{ old('makan_malam.' . $idx, $item) }}">
                                        @if ($idx === 0)
                                            <button type="button" class="btn btn-outline-primary add-makan-malam">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-danger remove-input">
                                                <i class="bx bx-minus"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
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

    const menuNumber       = document.getElementById('menu_number');
    const serveDaysInput   = document.getElementById('serve_days');
    const serveDaysPreview = document.getElementById('serve_days_preview');
    const namaMenu         = document.getElementById('nama_menu');

    // INIT dari data lama (sudah ada di hidden)
    try {
        const currentDays = JSON.parse(serveDaysInput.value || '[]');
        if (currentDays && currentDays.length) {
            serveDaysPreview.value = currentDays.join(', ');
        }
    } catch (_) { /* no-op */ }

    // Ubah saat select berubah
    menuNumber.addEventListener('change', () => {
        const n = parseInt(menuNumber.value, 10);
        const days = serveDaysFor(n);
        serveDaysInput.value = JSON.stringify(days);
        serveDaysPreview.value = days.length ? days.join(', ') : 'Belum dipilih';
        namaMenu.value = `Menu ${n}`;
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
