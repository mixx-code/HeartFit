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
                    action="{{ route('admin.menuMakanan.update', $menuMakanan->id) }}"
                    enctype="multipart/form-data"
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

                        {{-- Upload Foto --}}
                        <div class="mb-3">
                            <label class="form-label" for="foto_makanan">
                                <i class="bx bx-image"></i> Foto Makanan
                            </label>
                            <input type="file" id="foto_makanan" name="foto_makanan_single" class="form-control" 
                                accept="image/jpeg,image/jpg,image/png">
                            <small class="text-muted d-block mt-1">
                                Upload foto menu (jpeg, jpg, png). Maksimal 5 foto, 2MB per foto. Klik "Tambah Foto" untuk upload bertahap.
                            </small>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addFoto()">
                                <i class="bx bx-plus"></i> Tambah Foto
                            </button>
                            
                            @if($menuMakanan->foto_makanan && count($menuMakanan->foto_makanan) > 0)
                                <div class="mt-3">
                                    <p class="text-muted small mb-2">Foto yang sudah ada:</p>
                                    <div class="row g-2" id="existingPhotos">
                                        @foreach($menuMakanan->foto_makanan as $index => $foto)
                                            <div class="col-3" data-foto-index="{{ $index }}">
                                                <div class="position-relative">
                                                    <img src="{{ asset('storage/' . $foto) }}" class="img-fluid rounded" 
                                                         style="max-height: 100px; width: 100%; object-fit: cover;">
                                                    <div class="position-absolute top-0 end-0 m-1">
                                                        <small class="badge bg-secondary me-1">{{ $index + 1 }}</small>
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeExistingFoto({{ $index }})" title="Hapus foto">
                                                            <i class="bx bx-x"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mt-3">
                                <p class="text-muted small mb-2">Foto baru yang akan diupload:</p>
                                <div id="fotoPreview" class="row g-2"></div>
                            </div>
                            
                            <!-- Hidden fields untuk menyimpan foto baru -->
                            <div id="hiddenFotoContainer"></div>
                        <input type="hidden" id="removed_fotos" name="removed_fotos" value="">
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
    // ====== Foto Upload Bertahap ======
    let uploadedFiles = []; // Array untuk menyimpan file yang sudah diupload
    const fotoInput = document.getElementById('foto_makanan');
    const fotoPreview = document.getElementById('fotoPreview');
    const hiddenContainer = document.getElementById('hiddenFotoContainer');
    
    // Fungsi untuk menambah foto
    window.addFoto = function() {
        if (!fotoInput.files || fotoInput.files.length === 0) {
            alert('Pilih foto terlebih dahulu');
            return;
        }
        
        const file = fotoInput.files[0];
        const totalFotos = uploadedFiles.length + getExistingFotosCount();
        
        if (totalFotos >= 5) {
            alert('Maksimal 5 foto');
            return;
        }
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Tambah ke array
                uploadedFiles.push(file);
                
                // Tambah preview
                const col = document.createElement('div');
                col.className = 'col-3';
                col.setAttribute('data-foto-index', uploadedFiles.length - 1);
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 100px; width: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeNewFoto(${uploadedFiles.length - 1})">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                `;
                fotoPreview.appendChild(col);
                
                // Tambah hidden field
                addHiddenField(file, uploadedFiles.length - 1);
                
                // Reset input
                fotoInput.value = '';
            };
            reader.readAsDataURL(file);
        } else {
            alert('File harus berupa gambar');
        }
    };
    
    // Fungsi untuk menghapus foto baru
    window.removeNewFoto = function(index) {
        // Hapus dari array
        uploadedFiles.splice(index, 1);
        
        // Hapus preview
        const element = document.querySelector(`#fotoPreview [data-foto-index="${index}"]`);
        if (element) element.remove();
        
        // Rebuild hidden fields
        rebuildHiddenFields();
    };
    
    // Fungsi untuk menambah hidden field
    function addHiddenField(file, index) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'file';
        hiddenInput.name = `foto_makanan[${index}]`;
        hiddenInput.style.display = 'none';
        
        // Buat DataTransfer untuk menyimpan file
        const dt = new DataTransfer();
        dt.items.add(file);
        hiddenInput.files = dt.files;
        
        hiddenContainer.appendChild(hiddenInput);
    }
    
    // Fungsi untuk rebuild hidden fields
    function rebuildHiddenFields() {
        hiddenContainer.innerHTML = '';
        uploadedFiles.forEach((file, index) => {
            addHiddenField(file, index);
        });
        
        // Update index di preview
        const previews = fotoPreview.querySelectorAll('[data-foto-index]');
        previews.forEach((element, index) => {
            element.setAttribute('data-foto-index', index);
            const button = element.querySelector('button');
            button.setAttribute('onclick', `removeNewFoto(${index})`);
        });
    }
    
    // Fungsi untuk menghitung foto yang sudah ada
    function getExistingFotosCount() {
        const existingPhotos = document.querySelectorAll('#existingPhotos .col-3:not(.removed)');
        return existingPhotos.length;
    }
    
    // Validasi saat submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const totalFotos = uploadedFiles.length + getExistingFotosCount();
        if (totalFotos > 5) {
            e.preventDefault();
            alert('Maksimal 5 foto total');
            return false;
        }
    });

    // ====== Remove Existing Foto ======
    window.removeExistingFoto = function(index) {
        console.log('Removing existing foto:', index);
        
        // Hide the foto element
        const fotoElement = document.querySelector(`[data-foto-index="${index}"]`);
        if (fotoElement) {
            fotoElement.style.display = 'none';
            fotoElement.classList.add('removed');
        }
        
        // Track removed fotos
        const removedFotosInput = document.getElementById('removed_fotos');
        const existingFotos = @json($menuMakanan->foto_makanan ?? []);
        const removedFotos = removedFotosInput.value ? JSON.parse(removedFotosInput.value) : [];
        
        if (existingFotos[index]) {
            removedFotos.push(existingFotos[index]);
            removedFotosInput.value = JSON.stringify(removedFotos);
            console.log('Removed fotos:', removedFotos);
        }
    };

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
