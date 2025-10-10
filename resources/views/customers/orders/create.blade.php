@extends('layouts.app')
@section('title', 'Checkout Paket HeartFit')

@push('styles')
    <style>
        /* Base style */
        .selectable-card {
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color .15s ease, box-shadow .15s ease, transform .12s ease;
            position: relative;
            user-select: none;
        }

        .selectable-card:hover {
            transform: translateY(-2px);
        }

        /* ===============================
                               HIGHLIGHT SAAT DIPILIH
                               =============================== */

        /* Paket REGULER -> HIJAU */
        .btn-check:checked+.selectable-card.reguler {
            border-color: var(--bs-success);
            box-shadow: 0 0 0 .25rem rgba(25, 135, 84, .25);
        }

        .btn-check:checked+.selectable-card.reguler::after {
            content: "✓";
            position: absolute;
            top: 8px;
            right: 10px;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            background: var(--bs-success);
            border-radius: 999px;
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            font-size: 14px;
        }

        /* Paket PREMIUM -> BIRU */
        .btn-check:checked+.selectable-card.premium {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
        }

        .btn-check:checked+.selectable-card.premium::after {
            content: "✓";
            position: absolute;
            top: 8px;
            right: 10px;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            background: var(--bs-primary);
            border-radius: 999px;
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            font-size: 14px;
        }

        /* Metode BAYAR -> BIRU */
        .btn-check:checked+.selectable-card.pay {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
        }

        .btn-check:checked+.selectable-card.pay::after {
            content: "✓";
            position: absolute;
            top: 8px;
            right: 10px;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            background: var(--bs-primary);
            border-radius: 999px;
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            font-size: 14px;
        }

        /* ===============================
                               AKSESIBILITAS (keyboard focus)
                               =============================== */
        .btn-check:focus+.selectable-card,
        .btn-check:focus-visible+.selectable-card {
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .35);
        }

        /* ===== MENU PREVIEW – nicer UI ===== */
        #menuPreview {
            --gap: 12px;
        }

        .menu-day {
            border: 1px solid #eef0f3;
            border-radius: 14px;
            padding: 10px 12px 12px;
            margin-bottom: 14px;
            background: #fcfcfd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .03);
        }

        .menu-date {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #475569;
            /* slate-600 */
            font-weight: 600;
        }

        .menu-date .chip {
            font-size: .75rem;
            line-height: 1;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 999px;
            background: #f1f5f9;
            /* slate-100 */
            border: 1px solid #e2e8f0;
            /* slate-200 */
            color: #334155;
            /* slate-700 */
        }

        .menu-list {
            display: grid;
            gap: 12px;
            margin-top: 10px;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }

        .menu-card.menu-mini {
            border: 1px solid #eef0f3;
            border-radius: 12px;
            background: #fff;
            padding: 12px 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .04);
            transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
        }

        .menu-card.menu-mini:hover {
            transform: translateY(-2px);
            border-color: #dbe3ff;
            box-shadow: 0 10px 22px rgba(13, 110, 253, .08);
        }

        .menu-mini .menu-title {
            font-weight: 700;
            margin: 0 0 4px;
            color: #0f172a;
            /* slate-900 */
        }

        .menu-mini .menu-sub {
            font-size: .85rem;
            color: #64748b;
            /* slate-500 */
        }

        .menu-mini .row-inline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .menu-mini .badge-soft {
            font-size: .7rem;
            padding: 4px 8px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px dashed #e2e8f0;
            color: #475569;
        }

        .menu-empty {
            border: 1px dashed #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            color: #94a3b8;
            background: #f8fafc;
        }
        
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="py-5 bg-white border rounded-3">
            <div class="container">

                <h3 class="fw-bold text-primary text-center mb-1">Checkout Paket HeartFit</h3>
                <p class="text-secondary text-center mb-4">Lengkapi langkah berikut untuk memesan paket Anda.</p>

                <div class="mb-4">
                    <div class="d-flex justify-content-between small text-uppercase fw-semibold">
                        <span>1. Pilih Paket</span><span>2. Tanggal</span><span>3. Rincian</span><span>4. Bayar</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div id="wizardProgress" class="progress-bar" role="progressbar" style="width:25%;"></div>
                    </div>
                </div>

                <form id="orderForm" action="{{ route('orders.preview') }}" method="POST" class="needs-validation"
                    novalidate>
                    @csrf

{{-- STEP 1 --}}
<div class="wizard-step" data-step="1">
  <h5 class="fw-bold mb-3">Pilih Paket</h5>

  @php
    use Illuminate\Support\Str;
    // Group 1: kategori (Reguler/Premium/…)
    $byCategory = $packages->groupBy(fn($p) => optional($p->packageType)->packageType ?? 'Lainnya');
  @endphp

  @forelse ($byCategory as $typeLabel => $items)
    <div class="mb-2"><span class="badge bg-secondary">{{ $typeLabel }}</span></div>

    @php
      // Group 2: jenis_paket (harian/mingguan/bulanan/…)
      $byJenis = $items->groupBy(fn($p) => Str::lower(trim($p->jenis_paket ?? 'lainnya')));
    @endphp

    <div class="row g-3 mb-4">
      @foreach ($byJenis as $jenisKey => $variants)
        @php
          // id unik untuk kaitan elemen di kartu ini
          $gid = Str::slug('grp_'.$typeLabel.'_'.$jenisKey.'_'.$loop->index, '_');

          // --- DEFAULT VARIAN = durasi (total_hari) terbesar ---
          $variantsSorted = $variants->sortByDesc(fn($v) => (int) ($v->total_hari ?? 0));
          /** @var \App\Models\MealPackages $first */
          $first = $variantsSorted->first();

          // warna highlight khusus utk Reguler/Premium
          $cardClass = in_array(Str::lower($typeLabel), ['reguler','premium']) ? Str::lower($typeLabel) : '';
        @endphp

        <div class="col-md-3">
          <label class="w-100">
            {{-- Radio: value mengikuti varian terpilih di dropdown --}}
            <input
              type="radio"
              name="package_key"
              class="btn-check pkg-radio"
              value="{{ $first->id }}"
              data-group="{{ $gid }}"
              required
            >

            <div class="card border-0 shadow-sm h-100 selectable-card {{ $cardClass }}" id="card_{{ $gid }}">
              <div class="card-body">
                <div class="small text-muted text-uppercase">
                  {{ $first->nama_meal_package }}
                </div>

                <div class="h5 fw-bold mt-1 price-display" data-group="{{ $gid }}">
                  {{ number_format((int) round($first->price), 0, ',', '.') }}
                </div>

                <small class="text-muted">{{ $typeLabel }}</small>

                {{-- DROPDOWN VARIAN (porsi_paket) --}}
                <div class="mt-2">
                    <small class="text-black">Pilih Varian Paket</small>
                  <select class="form-select form-select-sm variant-select" data-group="{{ $gid }}">
                    @foreach ($variantsSorted as $v)
                      <option
                        value="{{ $v->id }}"
                        data-price="{{ (int) round($v->price) }}"
                        data-days="{{ (int) ($v->total_hari ?? 0) }}"
                        data-label="{{ e($v->nama_meal_package) }}"
                        @selected($v->id === $first->id)
                      >
                        {{ $v->porsi_paket }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mt-2">
                  <span class="badge bg-light text-dark duration-badge" data-for-group="{{ $gid }}">
                    Durasi: {{ (int) ($first->total_hari ?? 0) }} hari
                  </span>
                </div>
              </div>
            </div>
          </label>
        </div>
      @endforeach
    </div>
  @empty
    <div class="alert alert-warning">Belum ada paket untuk ditampilkan.</div>
  @endforelse

  <div class="d-flex justify-content-end mt-4">
    <button type="button" class="btn btn-primary" data-next>Berikutnya</button>
  </div>
</div>




                    {{-- STEP 2 --}}
                    <div class="wizard-step d-none" data-step="2">
                        <h5 class="fw-bold mb-3">Pilih Periode Aktif Paket</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Mulai Tanggal</label>
                                <input type="date" name="start_date" id="startDate" class="form-control" required>
                                <div class="form-text">Mulai minimal <strong>besok</strong>.</div>
                                <div class="invalid-feedback">Pilih tanggal mulai (≥ besok).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" id="endDate" class="form-control" readonly required>
                                <div class="form-text">Otomatis dihitung dari durasi paket.</div>
                                <div class="invalid-feedback">Tanggal selesai otomatis (tidak perlu diubah).</div>
                            </div>
                        </div>

                        <div class="alert alert-light border mt-3">
                            <div class="small mb-1">
                                <span class="me-2">Durasi paket: <strong id="durationInfo">-</strong> hari</span>
                                <span>Periode: <strong id="periodInfo">-</strong></span>
                            </div>
                        </div>

                        {{-- PREVIEW MENU MAKANAN (auto dari serve_days) --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                                <h6 class="mb-0">Menu Makanan Sesuai Periode</h6>
                                <button type="button" id="refreshMenu" class="btn btn-sm btn-outline-primary">
                                    Refresh
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="menuPreview" class="small text-muted">
                                    Pilih paket & tanggal untuk melihat menu.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-prev>Sebelumnya</button>
                            <button type="button" class="btn btn-primary" data-next>Berikutnya</button>
                        </div>
                    </div>


                    {{-- STEP 3 --}}
                    <div class="wizard-step d-none" data-step="3">
                        <h5 class="fw-bold mb-3">Rincian Pesanan</h5>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Paket</dt>
                                            <dd class="col-sm-7" id="summaryPackage">-</dd>

                                            <dt class="col-sm-5">Kategori</dt>
                                            <dd class="col-sm-7" id="summaryCategory">-</dd>

                                            <dt class="col-sm-5">Batch</dt>
                                            <dd class="col-sm-7" id="summaryBatch">-</dd>

                                            <dt class="col-sm-5">Harga</dt>
                                            <dd class="col-sm-7 fw-bold" id="summaryPrice">-</dd>
                                        </dl>
                                    </div>

                                    <div class="col-md-6">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Mulai</dt>
                                            <dd class="col-sm-7" id="summaryStart">-</dd>

                                            <dt class="col-sm-5">Selesai</dt>
                                            <dd class="col-sm-7" id="summaryEnd">-</dd>

                                            <dt class="col-sm-5">Durasi Paket</dt>
                                            <dd class="col-sm-7"><span id="summaryDays">0</span> hari</dd>

                                            <dt class="col-sm-5">Hari Layanan</dt>
                                            <dd class="col-sm-7">
                                                <span id="summaryActiveDays">0</span> hari
                                                <button class="btn btn-link btn-sm p-0 ms-1 align-baseline" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#serviceDatesCollapse">
                                                    lihat tanggal
                                                </button>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>

                                <div class="collapse mt-3" id="serviceDatesCollapse">
                                    <div class="border rounded p-2 bg-light small" id="summaryDatesList">
                                        <!-- diisi via JS -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MENU UNIK DALAM PERIODE --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                                <h6 class="mb-0">Daftar Menu Dalam Periode</h6>
                                {{-- <span class="badge text-bg-secondary">
                                    <span id="summaryMenuCount">0</span> menu
                                </span> --}}
                            </div>
                            <div class="card-body">
                                <div id="summaryMenuChips" class="d-flex flex-wrap gap-2 mb-2 " >
                                    <!-- preview chips -->
                                </div>
                                <div id="summaryMenuList" class="menu-list">
                                    <div class="menu-empty">Belum ada menu untuk periode ini.</div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 small mb-0">
                            Jadwal pengantaran & preferensi menu dapat diatur setelah checkout.
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-prev>Sebelumnya</button>
                            <button type="button" class="btn btn-primary" data-next>Lanjut ke Pembayaran</button>
                        </div>
                    </div>



                    {{-- STEP 4 --}}
                    <div class="wizard-step d-none" data-step="4">
                        <h5 class="fw-bold mb-3">Pilih Metode Pembayaran</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="w-100">
                                    <input type="radio" name="payment_method" class="btn-check" value="transfer"
                                        required>
                                    <div class="card border-0 shadow-sm h-100 selectable-card pay">
                                        <div class="card-body">
                                            <div class="h6 mb-1">Transfer</div><small class="text-muted">BCA • BNI • BRI •
                                                Mandiri • OVO • GoPay • DANA</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="w-100">
                                    <input type="radio" name="payment_method" class="btn-check" value="cod"
                                        required>
                                    <div class="card border-0 shadow-sm h-100 selectable-card pay">
                                        <div class="card-body">
                                            <div class="h6 mb-1">COD</div><small class="text-muted">Bayar di
                                                tempat</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-prev>Sebelumnya</button>
                            <button type="submit" class="btn btn-success">Lihat Ringkasan</button>
                        </div>
                    </div>

                    {{-- Hidden (optional) --}}
                    <input type="hidden" name="package_label" id="hidPackageLabel">
                    <input type="hidden" name="package_category" id="hidPackageCategory">
                    <input type="hidden" name="package_price" id="hidPackagePrice">

                    {{-- Tambahan agar data Step 3 ikut terkirim --}}
                    <input type="hidden" name="package_batch" id="hidPackageBatch">
                    <input type="hidden" name="service_dates" id="hidServiceDates">      {{-- JSON array --}}
                    <input type="hidden" name="unique_menus" id="hidUniqueMenus">         {{-- JSON array --}}
                    <input type="hidden" name="unique_menu_count" id="hidUniqueMenuCount">
                    <input type="hidden" name="amount_total" id="hidAmountTotal">         {{-- kalau = price, isi otomatis --}}

                </form>
            </div>
        </section>
    </div>

@push('scripts')
<script>
// ====== DATA DARI SERVER ======
const PACKAGES      = @json($packagesMap);   // { [id]: {id,label,category,price,durationDays,batch, ... } }
const MENUS_BATCH_I = @json($menusBatchI);   // [ {id,nama_menu,serve_days:[..],spec_menu:{...}}, ... ]

// ====== UTIL ======
const rupiah = n => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n);
const toYMD  = d => { const x=new Date(d); x.setHours(0,0,0,0);
  return `${x.getFullYear()}-${String(x.getMonth()+1).padStart(2,'0')}-${String(x.getDate()).padStart(2,'0')}`; };
const addDays = (ymd,n) => { const d=new Date(ymd); d.setDate(d.getDate()+n); return toYMD(d); };

// --- tanggal aman (hindari geser TZ) ---
const toLocalDate = (ymd) => {
  const [y,m,d] = ymd.split('-').map(Number);
  const dt = new Date(y, m-1, d, 0, 0, 0, 0);
  dt.setHours(0,0,0,0);
  return dt;
};
const dayOfMonth = (ymd) => toLocalDate(ymd).getDate(); // 1..31

// --- helper: update UI dalam satu kartu berdasarkan <option> aktif ---
function applyVariantToCard(groupId, opt){
  const price = Number(opt.dataset.price || 0);
  const days  = parseInt(opt.dataset.days || '0', 10);
  const label = opt.dataset.label || '';

  // update teks harga & durasi pada kartu
  const priceEl = document.querySelector(`.price-display[data-group="${groupId}"]`);
  if (priceEl) priceEl.textContent = new Intl.NumberFormat('id-ID').format(price);

  const durEl = document.querySelector(`.duration-badge[data-for-group="${groupId}"]`);
  if (durEl) durEl.textContent = `Durasi: ${days} hari`;

  // update value radio untuk kartu ini -> akan ikut ke Step 2 & summary
  const radio = document.querySelector(`.pkg-radio[data-group="${groupId}"]`);
  if (radio) radio.value = opt.value;

  // opsional: kalau kamu mau ganti label nama paket di summary pakai nama varian:
  // (fillSummary sudah mengandalkan PACKAGES[id], jadi aman)
}

// --- event: dropdown varian berubah ---
document.querySelectorAll('.variant-select').forEach(sel => {
  sel.addEventListener('change', () => {
    const gid = sel.dataset.group;
    const opt = sel.selectedOptions[0];
    applyVariantToCard(gid, opt);

    // jika kartu ini sedang terpilih, perbarui info periode/summary sekarang juga
    const radio = document.querySelector(`.pkg-radio[data-group="${gid}"]`);
    if (radio && radio.checked) {
      autoFillEndDate();
      updatePeriodInfo();
      if (startDate.value && endDate.value) {
        renderMenusLocalRange(startDate.value, endDate.value);
        if (current === 3) fillSummary();
      }
    }
  });
});

// --- saat pertama kali render, samakan UI tiap kartu ke opsi pertama ---
document.querySelectorAll('.variant-select').forEach(sel => {
  const gid = sel.dataset.group;
  const opt = sel.selectedOptions[0];
  if (opt) applyVariantToCard(gid, opt);
});

// --- helper day name utk label tanggal ---
const ID_DAYS = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const dateLabel = (ymd) => {
  const d = toLocalDate(ymd);
  return `${ID_DAYS[d.getDay()]}, ${ymd}`;
};

// ====== ELEMEN ======
const steps = Array.from(document.querySelectorAll('.wizard-step'));
const progressEl = document.getElementById('wizardProgress');
const nextBtns = document.querySelectorAll('[data-next]');
const prevBtns = document.querySelectorAll('[data-prev]');
const form = document.getElementById('orderForm');
const startDate = document.getElementById('startDate');
const endDate   = document.getElementById('endDate');
const menuPreviewEl  = document.getElementById('menuPreview');
const refreshMenuBtn = document.getElementById('refreshMenu');
let current = 1;

// ====== TANGGAL MINIMAL (BESOK) ======
const today = new Date();
const tomorrow = new Date(today); tomorrow.setDate(today.getDate()+1);
startDate.min = toYMD(tomorrow);
endDate.readOnly = true;

// ====== WIZARD ======
function showStep(n){
  steps.forEach(s=>s.classList.add('d-none'));
  document.querySelector(`.wizard-step[data-step="${n}"]`).classList.remove('d-none');
  current=n; if(progressEl) progressEl.style.width=(n*25)+'%';
}
function getChosenKey(){
  const el=document.querySelector('input[name="package_key"]:checked'); return el?el.value:null;
}
function getDuration(){
  const id=getChosenKey(); return id&&PACKAGES[id] ? (parseInt(PACKAGES[id].durationDays,10)||0) : 0;
}
function autoFillEndDate(){
  const dur=getDuration(); if(!startDate.value||!dur) return; endDate.value = addDays(startDate.value, dur-1);
}
function updatePeriodInfo(){
  const dur=getDuration();
  const durEl = document.getElementById('durationInfo');
  const perEl = document.getElementById('periodInfo');
  if (durEl) durEl.textContent = dur||'-';
  if (perEl) perEl.textContent = (startDate.value&&endDate.value)?`${startDate.value} s/d ${endDate.value}`:'-';
}

// Isi badge durasi di Step 1
// Kartu baru (data-for-group) di-set oleh applyVariantToCard saat init
document.querySelectorAll('.duration-badge').forEach(b => {
  const forId = b.getAttribute('data-for');
  const group = b.getAttribute('data-for-group');

  if (forId) {
    const days = PACKAGES[forId]?.durationDays;
    if (Number.isInteger(days)) {
      b.textContent = `Durasi: ${days} hari`;
    }
    // kalau undefined, biarkan teks Blade apa adanya (jangan di-'-kan)
  } 
  // if (group) -> do nothing: applyVariantToCard akan mengisi
});

// ====== SERVE DAYS ======
function menusForDate(ymd){
  const dom = dayOfMonth(ymd); // 1..31
  return MENUS_BATCH_I.filter(m => {
    const arr = Array.isArray(m.serve_days) ? m.serve_days : [];
    const days = arr.map(v => parseInt(v, 10)).filter(n => Number.isInteger(n));
    return days.includes(dom);
  });
}

// ====== RENDER: SPEC & CARD (modal detail) ======
function renderSpecModal(spec){
  if(!spec || typeof spec !== 'object') {
    return '<div class="alert alert-secondary mb-0">Detail menu belum tersedia.</div>';
  }
  const ORDER = [
    { key: 'Makan Pagi',  icon: '☀️' },
    { key: 'Makan Siang', icon: '🌤️' },
    { key: 'Makan Malam', icon: '🌙' },
    { key: 'Snack',       icon: '🍪' },
  ];
  const makeMiniCards = (items) => {
    if (!Array.isArray(items) || !items.length) {
      return `<div class="text-muted small">Tidak ada item.</div>`;
    }
    return `
      <div class="d-flex flex-wrap gap-2 mt-2">
        ${items.map(it => `
          <div class="card border shadow-sm bg-light-subtle" style="min-width: 120px; flex: 1 0 30%; max-width: 200px;">
            <div class="card-body p-2 text-center">
              <span class="fw-semibold small text-dark">${it}</span>
            </div>
          </div>
        `).join('')}
      </div>`;
  };
  const sectionCard = (title, items, icon) => `
    <div class="col-12">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2 border-start border-3 border-secondary-subtle">
          <span>${icon}</span><span>${title}</span>
        </div>
        <div class="card-body">
          ${makeMiniCards(items)}
        </div>
      </div>
    </div>`;
  const sections = ORDER
    .filter(s => Object.prototype.hasOwnProperty.call(spec, s.key))
    .map(s => sectionCard(s.key, spec[s.key], s.icon))
    .join('');
  if (!sections) {
    const raw = Object.entries(spec).map(([k, arr]) => sectionCard(k, arr, '📋')).join('');
    return `<div class="row g-3">${raw}</div>`;
  }
  return `<div class="row g-3">${sections}</div>`;
}

function renderCards(items){
  if(!items.length){
    return `<div class="alert alert-secondary mb-0 small">Belum ada menu untuk tanggal ini.</div>`;
  }
  return `
    <div class="row row-cols-1 g-2">
      ${items.map(it=>{
        const specStr = encodeURIComponent(JSON.stringify(it.spec_menu||{}));
        const nameStr = encodeURIComponent(it.nama_menu ?? '-');
        return `
          <div class="col">
            <div class="card h-100 shadow-sm border-0 position-relative menu-card"
                 data-menu-name="${nameStr}" data-menu-spec="${specStr}">
              <div class="card-body">
                <h6 class="card-title mb-1 text-dark fw-semibold">${it.nama_menu ?? '-'}</h6>
                <p class="card-text text-muted small mb-0">Klik untuk lihat detail</p>
                <a href="#" class="stretched-link" tabindex="-1"
                   aria-label="Lihat detail ${it.nama_menu ?? ''}"></a>
              </div>
            </div>
          </div>`;
      }).join('')}
    </div>`;
}

// ====== RENDER RANGE (PER TANGGAL) + ATTACH EVENTS ======
function renderMenusLocalRange(startYmd, endYmd){
  if(!menuPreviewEl){ console.warn('menuPreview element not found'); return; }
  if(!startYmd || !endYmd){
    menuPreviewEl.innerHTML = `
      <div class="alert alert-light border text-muted mb-0">
        Pilih paket & tanggal untuk melihat menu.
      </div>`;
    return;
  }

  const s = toLocalDate(startYmd), e = toLocalDate(endYmd);
  let cols = '';

  for(let dt = new Date(s); dt <= e; dt.setDate(dt.getDate()+1)){
    const ymd = toYMD(dt);
    const items = menusForDate(ymd);

    cols += `
      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white d-flex align-items-center justify-content-between py-2">
            <span class="badge rounded-pill text-bg-light border text-dark">${dateLabel(ymd)}</span>
            <span class="badge text-bg-secondary">${items.length} menu</span>
          </div>
          <div class="card-body">
            ${renderCards(items)}
          </div>
        </div>
      </div>`;
  }

  if(!cols.trim()){
    menuPreviewEl.innerHTML = `<div class="alert alert-secondary mb-0">Belum ada menu untuk periode ini.</div>`;
    return;
  }

  menuPreviewEl.innerHTML = `<div class="row g-3">${cols}</div>`;
  attachMenuCardEvents();
}

// ====== MODAL (Bootstrap) ======
let menuModal;
function ensureMenuModal(){
  if(!menuModal){
    const el=document.getElementById('menuDetailModal');
    if(!el){ console.warn('Modal element #menuDetailModal not found'); return null; }
    menuModal = new bootstrap.Modal(el,{backdrop:true,focus:true});
  }
  return menuModal;
}
function showMenuModal(menuName,spec){
  const titleEl=document.getElementById('menuDetailTitle');
  const bodyEl =document.getElementById('menuDetailBody');
  if(titleEl) titleEl.textContent = menuName || 'Detail Menu';
  if(bodyEl)  bodyEl.innerHTML   = renderSpecModal(spec);
  const inst=ensureMenuModal(); if(inst) inst.show();
}
function attachMenuCardEvents(){
  const cards=document.querySelectorAll('#menuPreview .menu-card');
  cards.forEach(card=>{
    card.addEventListener('click',()=>{
      const name=decodeURIComponent(card.dataset.menuName||'');
      const spec=JSON.parse(decodeURIComponent(card.dataset.menuSpec||'%7B%7D'));
      showMenuModal(name,spec);
    });
  });
}

// ====== SUMMARY (STEP 3) ======
function datesInRange(startYmd, endYmd){
  if(!startYmd || !endYmd) return [];
  const s = toLocalDate(startYmd), e = toLocalDate(endYmd);
  const out = [];
  for(let dt = new Date(s); dt <= e; dt.setDate(dt.getDate()+1)){
    out.push(toYMD(dt));
  }
  return out;
}
function renderDatesBadges(ymds){
  if(!ymds.length) return '<span class="text-muted">-</span>';
  return ymds.map(d => `<span class="badge rounded-pill text-bg-light border text-dark me-1 mb-1">${dateLabel(d)}</span>`).join('');
}
function renderMenuChips(names){
  const el = document.getElementById('summaryMenuChips');
  if(!el) return;
  if(!names.length){ el.innerHTML = ''; return; }
  const max = 8;
  const top = names.slice(0, max);
  const more = Math.max(0, names.length - top.length);
  el.innerHTML = top.map(n => `<span style="display: none;" class="badge text-bg-light border hidden">${n}</span>`).join('') +
                 (more ? ` <span style="display: none;" class="badge text-bg-secondary hidden">+${more} lainnya</span>` : '');
}
function collectUniqueMenus(startYmd, endYmd){
  if(!startYmd || !endYmd) return [];
  const s = toLocalDate(startYmd), e = toLocalDate(endYmd);
  const seen = new Set();
  const uniqueMenus = [];
  for(let dt = new Date(s); dt <= e; dt.setDate(dt.getDate()+1)){
    const ymd = toYMD(dt);
    const items = menusForDate(ymd);
    items.forEach(it=>{
      const raw = (it?.nama_menu ?? '').toString();
      const key = raw.trim().toLowerCase();
      if(key && !seen.has(key)){
        seen.add(key);
        uniqueMenus.push(raw.trim());
      }
    });
  }
  return uniqueMenus;
}
function renderSummaryMenuList(names){
  const listEl = document.getElementById('summaryMenuList');
  const countEl = document.getElementById('summaryMenuCount');
  if(!listEl) return;
  if(countEl) countEl.textContent = names.length;

  if(!names.length){
    listEl.innerHTML = `<div class="menu-empty">Belum ada menu untuk periode ini.</div>`;
    return;
  }
  listEl.innerHTML = names.map(n => `
    <div class="menu-card menu-mini">
      <div class="row-inline">
        <div>
          <p class="menu-title mb-0">${n}</p>
          <p class="menu-sub mb-0">Menu</p>
        </div>
      </div>
    </div>
  `).join('');
}
function fillSummary(){
  const id = getChosenKey();
  const pkg = id ? PACKAGES[id] : null;

  function setHidden(id, value){
    const el = document.getElementById(id);
    if (!el) return;
    if (Array.isArray(value) || (value && typeof value === 'object')) {
        el.value = JSON.stringify(value);
    } else {
        el.value = value ?? '';
    }
    }

  const packageNameEl  = document.getElementById('summaryPackage');
  const categoryEl     = document.getElementById('summaryCategory');
  const batchEl        = document.getElementById('summaryBatch');
  const startEl        = document.getElementById('summaryStart');
  const endEl          = document.getElementById('summaryEnd');
  const daysEl         = document.getElementById('summaryDays');
  const activeDaysEl   = document.getElementById('summaryActiveDays');
  const priceEl        = document.getElementById('summaryPrice');
  const datesListEl    = document.getElementById('summaryDatesList');

  const hidLabel   = document.getElementById('hidPackageLabel');
  const hidCat     = document.getElementById('hidPackageCategory');
  const hidPrice   = document.getElementById('hidPackagePrice');

  const label    = pkg?.label || pkg?.nama_meal_package || '-';
  const category = pkg?.category || (pkg?.packageTypeLabel ?? '-') || '-';
  const batch    = pkg?.batch || '-';
  const priceNum = Number(pkg?.price ?? 0);
  const dur      = getDuration();

  if(packageNameEl) packageNameEl.textContent = label;
  if(categoryEl)    categoryEl.textContent    = category;
  if(batchEl)       batchEl.textContent       = batch;
  if(daysEl)        daysEl.textContent        = dur || 0;
  if(priceEl)       priceEl.textContent       = rupiah(priceNum);

  const s = startDate.value || '';
  const e = endDate.value   || '';
  if(startEl) startEl.textContent = s || '-';
  if(endEl)   endEl.textContent   = e || '-';

  if(hidLabel) hidLabel.value = label;
  if(hidCat)   hidCat.value   = category;
  if(hidPrice) hidPrice.value = priceNum;

  const allDates = datesInRange(s, e);
  if(activeDaysEl) activeDaysEl.textContent = allDates.length || 0;
  if(datesListEl)  datesListEl.innerHTML   = renderDatesBadges(allDates);

  const names = (s && e) ? collectUniqueMenus(s, e) : [];
  const cntEl = document.getElementById('summaryMenuCount');
  if (cntEl) cntEl.textContent = names.length;

  

  renderMenuChips(names);
  renderSummaryMenuList(names);

  // Hitung amount_total (kalau belum ada logika ongkir/diskon, samakan dengan priceNum)
const amountTotal = priceNum;

// Set ke hidden fields supaya ikut POST ke preview/store
setHidden('hidPackageBatch', batch);
setHidden('hidServiceDates', allDates);
setHidden('hidUniqueMenus', names);
setHidden('hidUniqueMenuCount', names.length);
setHidden('hidAmountTotal', amountTotal);
}

// ====== EVENTS ======
startDate.addEventListener('change',()=>{
  if(new Date(startDate.value) < new Date(toYMD(tomorrow))) startDate.value=toYMD(tomorrow);
  autoFillEndDate(); updatePeriodInfo();
  if(startDate.value&&endDate.value){
    renderMenusLocalRange(startDate.value,endDate.value);
    if(current===3) fillSummary();
  }
});
document.querySelectorAll('input[name="package_key"]').forEach(radio=>{
  radio.addEventListener('change',()=>{
    document.querySelectorAll('.selectable-card').forEach(c=>c.classList.remove('border-primary','shadow'));
    const card = radio.closest('label')?.querySelector('.selectable-card'); if(card) card.classList.add('border-primary','shadow');
    if(startDate.value){
      autoFillEndDate(); updatePeriodInfo();
      if(endDate.value) {
        renderMenusLocalRange(startDate.value,endDate.value);
        if(current===3) fillSummary();
      }
    }
  });
});
if(refreshMenuBtn){
  refreshMenuBtn.addEventListener('click',()=>{
    if(startDate.value&&endDate.value) renderMenusLocalRange(startDate.value,endDate.value);
  });
}
nextBtns.forEach(btn=>btn.addEventListener('click',()=>{
  if(current===1){
    if(!getChosenKey()){ alert('Pilih salah satu paket terlebih dahulu.'); return; }
    showStep(2);
    if(!startDate.value) startDate.value = toYMD(tomorrow);
    autoFillEndDate(); updatePeriodInfo();
    renderMenusLocalRange(startDate.value,endDate.value);
  } else if(current===2){
    const invalid = !startDate.value || new Date(startDate.value) < new Date(toYMD(tomorrow)) || !endDate.value;
    startDate.classList.toggle('is-invalid', !startDate.value || new Date(startDate.value) < new Date(toYMD(tomorrow)));
    endDate.classList.toggle('is-invalid', !endDate.value);
    if(invalid) return;
    startDate.classList.remove('is-invalid'); endDate.classList.remove('is-invalid');
    fillSummary();
    showStep(3);
  } else if(current===3){
    showStep(4);
    const payChecked = document.querySelector('input[name="payment_method"]:checked');
    if (!payChecked) {
        const first = document.querySelector('input[name="payment_method"][value="transfer"]');
        if (first) first.checked = true;
    }
    }
}));
prevBtns.forEach(btn=>btn.addEventListener('click',()=>{ if(current>1) showStep(current-1); }));
form.addEventListener('submit', e => {
  if(!form.checkValidity()){ e.preventDefault(); e.stopPropagation(); }
  // backup: set hidden lagi sebelum submit
  if (current >= 3) fillSummary();
  form.classList.add('was-validated');
});

// ====== START ======
showStep(1);
</script>
@endpush





    <div class="modal fade" id="menuDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0" id="menuDetailTitle">Detail Menu</h5>
                        <small id="menuDetailSub" class="text-muted"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-3 p-md-4" id="menuDetailBody">
                    <!-- diisi via JS -->
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


@endsection
