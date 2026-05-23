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
                    <form method="POST" action="{{ route('admin.menuMakanan.store') }}" enctype="multipart/form-data">
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

                        {{-- Upload Foto --}}
                        <div class="mb-3">
                            <label class="form-label" for="foto_makanan">
                                <i class="bx bx-image"></i> Foto Makanan
                            </label>
                            <input type="file" id="foto_makanan" name="foto_makanan_single" class="form-control" 
                                accept="image/jpeg,image/jpg,image/png">
                            <small class="text-muted d-block mt-1">
                                Upload foto menu (jpeg, jpg, png). Maksimal 5 foto, 2MB per foto. Pilih file untuk langsung menambahkan.
                            </small>
                            
                            <div class="mt-3">
                                <p class="text-muted small mb-2">Foto yang akan diupload:</p>
                                <div id="fotoPreview" class="row g-2"></div>
                            </div>
                            
                            <!-- Hidden fields untuk menyimpan foto -->
                            <div id="hiddenFotoContainer"></div>
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
            // ====== Foto Upload Bertahap ======
            let uploadedFiles = []; // Array {file, label}
            const fotoInput = document.getElementById('foto_makanan');
            const fotoPreview = document.getElementById('fotoPreview');
            const hiddenContainer = document.getElementById('hiddenFotoContainer');

            // Auto-add saat pilih file
            fotoInput.addEventListener('change', function() {
                if (!this.files || this.files.length === 0) return;

                const file = this.files[0];

                if (uploadedFiles.length >= 5) {
                    alert('Maksimal 5 foto');
                    this.value = '';
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    alert('File harus berupa gambar');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const idx = uploadedFiles.length;
                    uploadedFiles.push({ file: file, label: '' });

                    // Tambah preview + input keterangan
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-3';
                    col.setAttribute('data-foto-index', idx);
                    col.innerHTML = `
                        <div class="card border shadow-sm h-100">
                            <div class="position-relative">
                                <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeNewFoto(${idx})">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                            <div class="card-body p-2">
                                <input type="text" class="form-control form-control-sm foto-label-input" 
                                    placeholder="Keterangan foto (misal: Makan Siang)"
                                    data-label-index="${idx}"
                                    oninput="updateFotoLabel(${idx}, this.value)">
                            </div>
                        </div>
                    `;
                    fotoPreview.appendChild(col);

                    // Tambah hidden field
                    addHiddenField(file, idx);

                    // Reset input supaya bisa pilih file lagi
                    fotoInput.value = '';
                };
                reader.readAsDataURL(file);
            });

            // Update label di array
            window.updateFotoLabel = function(index, value) {
                if (uploadedFiles[index]) {
                    uploadedFiles[index].label = value;
                }
                // Update hidden label field
                const labelInput = hiddenContainer.querySelector(`input[name="foto_label[${index}]"]`);
                if (labelInput) labelInput.value = value;
            };
            
            // Fungsi untuk menghapus foto
            window.removeNewFoto = function(index) {
                uploadedFiles.splice(index, 1);
                const element = document.querySelector(`#fotoPreview [data-foto-index="${index}"]`);
                if (element) element.remove();
                rebuildHiddenFields();
            };
            
            // Fungsi untuk menambah hidden field
            function addHiddenField(file, index) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'file';
                hiddenInput.name = `foto_makanan[${index}]`;
                hiddenInput.style.display = 'none';
                const dt = new DataTransfer();
                dt.items.add(file);
                hiddenInput.files = dt.files;
                hiddenContainer.appendChild(hiddenInput);

                // Hidden label field
                const labelInput = document.createElement('input');
                labelInput.type = 'hidden';
                labelInput.name = `foto_label[${index}]`;
                labelInput.value = uploadedFiles[index]?.label || '';
                hiddenContainer.appendChild(labelInput);
            }
            
            // Fungsi untuk rebuild hidden fields
            function rebuildHiddenFields() {
                hiddenContainer.innerHTML = '';
                uploadedFiles.forEach((item, index) => {
                    addHiddenField(item.file, index);
                });
                
                // Update index di preview
                const previews = fotoPreview.querySelectorAll('[data-foto-index]');
                previews.forEach((element, index) => {
                    element.setAttribute('data-foto-index', index);
                    const button = element.querySelector('button');
                    button.setAttribute('onclick', `removeNewFoto(${index})`);
                    const labelInput = element.querySelector('.foto-label-input');
                    if (labelInput) {
                        labelInput.setAttribute('data-label-index', index);
                        labelInput.setAttribute('oninput', `updateFotoLabel(${index}, this.value)`);
                    }
                });
            }
            
            // Validasi saat submit
            document.querySelector('form').addEventListener('submit', function(e) {
                if (uploadedFiles.length > 5) {
                    e.preventDefault();
                    alert('Maksimal 5 foto');
                    return false;
                }
            });

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
