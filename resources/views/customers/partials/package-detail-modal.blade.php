{{-- ============================================================
     REUSABLE MODAL DETAIL PAKET
     Cara pakai: @include('customers.partials.package-detail-modal')
     Tombol pemicu: gunakan data attribute, contoh:
       <button
         class="btn btn-outline-primary btn-detail-paket"
         data-package-key="reguler">
         Detail Paket
       </button>
     ============================================================ --}}

@php
/**
 * Siapkan data semua paket dalam satu array JSON.
 * Masing-masing key = nama paket (reguler/premium/personal).
 */
$modalData = [];
foreach ($packages as $key => $pkg) {
    $firstPackage = $pkg['meal_packages']->first();
    // Kirim object menu lengkap, bukan hanya nama
    $menusArray = is_array($pkg['menus']) ? $pkg['menus'] : $pkg['menus']->toArray();
    $menus = array_slice($menusArray, 0, 3); // Ambil 3 pertama
    $menus = array_map(function($m) {
        return [
            'nama_menu' => $m['nama_menu'],
            'spec_menu' => $m['spec_menu']
        ];
    }, $menus);

    $modalData[$key] = [
        'type'        => $pkg['type'],
        'price'       => $firstPackage ? number_format($firstPackage->price, 0, ',', '.') : null,
        'detail'      => $firstPackage?->detail_paket,
        'porsi'       => $firstPackage?->porsi_paket,
        'total_hari'  => $firstPackage?->total_hari,
        'menus'       => $menus,
        'is_personal' => $key === 'personal',
    ];
}

// Konfigurasi tampilan per tipe paket
$packageConfig = [
    'reguler'  => ['color' => 'primary',   'icon' => 'bx-package', 'default_price' => '50.000',  'default_desc' => 'Paket hemat dengan menu seimbang untuk kebutuhan harian Anda.'],
    'premium'  => ['color' => 'warning',   'icon' => 'bx-crown',   'default_price' => '400.000', 'default_desc' => 'Paket lengkap dengan menu premium untuk kebutuhan nutrisi optimal.'],
    'personal' => ['color' => 'danger',    'icon' => 'bx-user',    'default_price' => '700.000', 'default_desc' => 'Paket personal dengan menu premium plus konsultasi ahli gizi.'],
];

$durations = [
    ['hari' => '4 Hari',  'kali' => '2 Kali Makan', 'badge' => 'success'],
    ['hari' => '8 Hari',  'kali' => '1 Kali Makan', 'badge' => 'info'],
    ['hari' => '12 Hari', 'kali' => '2 Kali Makan', 'badge' => 'warning'],
    ['hari' => '24 Hari', 'kali' => '1 Kali Makan', 'badge' => 'warning'],
    ['hari' => '36 Hari', 'kali' => '2 Kali Makan', 'badge' => 'warning'],
    ['hari' => '72 Hari', 'kali' => '1 Kali Makan', 'badge' => 'warning'],
];
@endphp

{{-- Update CSS untuk tampilan Premium & Bersih --}}
<style>
    /* Styling khusus untuk area durasi agar sesuai gambar referensi namun lebih rapi */
    #packageDetailModal .duration-container {
        margin-top: 1.5rem;
    }

    #packageDetailModal .duration-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 0;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s;
    }

    #packageDetailModal .duration-item:last-child {
        border-bottom: none;
    }

    #packageDetailModal .duration-label {
        font-weight: 500;
        color: #64748b;
        font-size: 0.95rem;
    }

    /* Badge Durasi - Memperbaiki masalah warna solid di gambar */
    #packageDetailModal .duration-badge {
        min-width: 120px;
        text-align: center;
        padding: 8px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        /* Memberikan efek glassmorphism/soft background */
        border: 1px solid transparent;
    }

    /* Variasi Warna Badge (Soft Style) */
    .badge-soft-success { background-color: #ecfdf5; color: #10b981; border-color: #d1fae5 !important; }
    .badge-soft-info { background-color: #f0f9ff; color: #0ea5e9; border-color: #e0f2fe !important; }
    .badge-soft-warning { background-color: #fffbeb; color: #f59e0b; border-color: #fef3c7 !important; }

    /* Scrollbar untuk area menu di kanan */
    .menu-scroll-area {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 10px;
    }
    .menu-scroll-area::-webkit-scrollbar { width: 4px; }
    .menu-scroll-area::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    /* Tombol Close Merah dengan X Putih */
    #packageDetailModal .btn-close {
        background-color: #dc3545 !important;
        border: none !important;
        border-radius: 50% !important;
        width: 32px !important;
        height: 32px !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        opacity: 1 !important;
        transition: all 0.2s ease !important;
    }

    #packageDetailModal .btn-close:hover {
        background-color: #bb2d3b !important;
    }

    #packageDetailModal .btn-close::before {
        content: '×' !important;
        color: white !important;
        font-size: 20px !important;
        font-weight: bold !important;
        line-height: 1 !important;
    }

    /* Additional styling untuk menu cards */
    .menu-card {
        background: white;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }

    .menu-card:hover {
        background-color: #f8fafc;
        border-color: #e2e8f0;
    }

    .category-label {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-bottom: 0.5rem;
    }

    .food-item {
        padding: 0.25rem 0;
        font-size: 0.9rem;
        color: #475569;
    }

    /* Animasi untuk modal utama */
    #packageDetailModal {
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
    #packageDetailModal.show {
        opacity: 1;
    }
</style>

<div class="modal fade d-none" id="packageDetailModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            
            <div class="modal-header text-white p-4" id="modalHeader" style="border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
                        <i class="bx fs-3" id="modalIcon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="modalTitle">Detail Paket</h5>
                        <small class="opacity-75" id="modalMeta"></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 p-lg-5" id="modalBody">
                <div class="row">
                    <div class="col-md-5 border-end">
                        <div class="mb-4">
                            <h3 class="fw-bold mb-0 text-dark" id="modalPrice">Rp 0</h3>
                            <p class="text-muted small" id="modalDesc"></p>
                        </div>

                        <div class="duration-container">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bx bx-time-five me-2 text-muted"></i> Pilihan Durasi
                            </h6>
                            <div class="duration-list">
                                @foreach($durations as $d)
                                <div class="duration-item">
                                    <span class="duration-label">{{ $d['hari'] }}</span>
                                    <span class="duration-badge badge-soft-{{ $d['badge'] }}">
                                        {{ $d['kali'] }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div id="modalConsultation" class="mt-4 d-none">
                            <div class="alert alert-info border-0 rounded-3 p-3 mb-0">
                                <div class="d-flex">
                                    <i class="bx bx-info-circle fs-4 me-2"></i>
                                    <small class="fw-bold">Termasuk konsultasi harian dengan ahli gizi kami.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7 ps-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-4 mt-md-0">
                            <h6 class="fw-bold mb-0" id="modalMenuTitle">Menu Paket</h6>
                            <span class="badge bg-light text-muted fw-normal">Siklus 3 Hari</span>
                        </div>

                        <div id="modalMenus" class="menu-scroll-area">
                            </div>

                        <div class="mt-4">
                            <a href="{{ route('orders.create') }}" class="btn btn-primary w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center" id="modalOrderBtn" style="border-radius: 12px; transition: 0.3s;">
                                <span id="modalOrderText">Pesan Sekarang</span>
                                <i class="bx bx-right-arrow-alt ms-2 fs-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Logika Data & Konfigurasi tetap sama sesuai instruksi awal, 
 * hanya mengupdate bagian Rendering HTML di dalam function JS.
 */
const PACKAGE_DATA = @json($modalData);
const PACKAGE_CONFIG = @json($packageConfig);

function openPackageModal(packageKey) {
    console.log('Opening package modal for:', packageKey);
    
    const data   = PACKAGE_DATA[packageKey];
    const config = PACKAGE_CONFIG[packageKey];

    console.log('Package data:', data);
    console.log('Package config:', config);

    if (!data || !config) return;

    // 1. Update Header & Button Color
    const header = document.getElementById('modalHeader');
    header.className = `modal-header text-white bg-${config.color}`;
    document.getElementById('modalIcon').className = `bx ${config.icon} text-${config.color}`;
    document.getElementById('modalOrderBtn').style.backgroundColor = `var(--bs-${config.color})`;
    
    // 2. Update Text Content
    document.getElementById('modalTitle').textContent = `Detail Paket ${data.type}`;
    document.getElementById('modalPrice').textContent = data.price ? `Rp ${data.price}` : `Rp ${config.default_price}`;
    document.getElementById('modalDesc').textContent  = data.detail || config.default_desc;
    document.getElementById('modalMeta').textContent  = data.porsi ? `Porsi: ${data.porsi} | Durasi: ${data.total_hari} hari` : '';
    document.getElementById('modalConsultation').classList.toggle('d-none', !data.is_personal);

    // 3. Render Menu
    const menusEl = document.getElementById('modalMenus');
    const menus = data.menus.length > 0 ? data.menus : [];
    
    console.log('Modal menus data:', menus);
    console.log('First menu with photos:', menus[0]);
    
    let menuHtml = '';
    
    if (menus.length > 0) {
        menuHtml = menus.map((menu, i) => {
            console.log(`Menu ${i}:`, {
                nama_menu: menu.nama_menu,
                foto_makanan: menu.foto_makanan,
                has_fotos: menu.foto_makanan && menu.foto_makanan.length > 0
            });
            
            let detailHtml = '';
            let fotoHtml = '';
            
            // Tambahkan foto menu jika ada
            if (menu.foto_makanan && menu.foto_makanan.length > 0) {
                fotoHtml = `
                    <div class="mb-3">
                        <h6 class="mb-2 d-flex align-items-center gap-2">
                            <i class="bx bx-image text-primary"></i>
                            <span>Foto Menu</span>
                        </h6>
                        <div class="row g-2">
                            ${menu.foto_makanan.map((foto, index) => {
                                const fotoPath = (typeof foto === 'object' && foto.path) ? foto.path : foto;
                                const fotoLabel = (typeof foto === 'object' && foto.label) ? foto.label : ('Foto ' + (index + 1));
                                return `
                                <div class="col-md-4 col-6">
                                    <div class="card border-0 shadow-sm overflow-hidden">
                                        <div class="position-relative" style="height: 120px;">
                                            <img src="/storage/${fotoPath}" 
                                                 class="w-100 h-100" 
                                                 style="object-fit: cover; transition: transform 0.3s ease;"
                                                 onmouseover="this.style.transform='scale(1.05)'"
                                                 onmouseout="this.style.transform='scale(1)'"
                                                 onclick="window.open('/storage/${fotoPath}', '_blank')"
                                                 alt="${fotoLabel}">
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-dark bg-opacity-75 text-white">
                                                    <i class="bx bx-expand fs-6"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body p-2 text-center">
                                            <small class="text-muted">${fotoLabel}</small>
                                        </div>
                                    </div>
                                </div>
                            `}).join('')}
                        </div>
                    </div>
                `;
            } else {
                console.log(`Menu ${i} has no photos`);
            }
            
            if (menu.spec_menu) {
                Object.entries(menu.spec_menu).forEach(([kategori, makananList]) => {
                    detailHtml += `
                        <div class="mt-2">
                            <span class="category-label text-${config.color}">${kategori}</span>
                            ${makananList.map(makanan => `
                                <div class="food-item">
                                    <i class="bx bx-check text-success me-2"></i>
                                    <span>${makanan}</span>
                                </div>
                            `).join('')}
                        </div>
                    `;
                });
            }
            
            return `
                <div class="menu-card">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-light rounded-3 p-2 me-3">
                            <i class="bx bx-restaurant fs-5 text-secondary"></i>
                        </div>
                        <h6 class="fw-bold mb-0">${menu.nama_menu}</h6>
                    </div>
                    ${fotoHtml}
                    ${detailHtml}
                </div>
            `;
        }).join('');
    } else {
        menuHtml = `<div class="text-center py-5 text-muted small">Data menu belum tersedia</div>`;
    }
    
    menusEl.innerHTML = menuHtml;

    // Show Modal
    const modalElement = document.getElementById('packageDetailModal');
    
    // Hapus class d-none dan tampilkan dengan animasi
    setTimeout(() => {
        modalElement.classList.remove('d-none');
        setTimeout(() => {
            modalElement.classList.add('show');
        }, 50);
    }, 100);
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}
</script>