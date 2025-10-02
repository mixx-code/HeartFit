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
.btn-check:checked + .selectable-card.reguler {
    border-color: var(--bs-success);
    box-shadow: 0 0 0 .25rem rgba(25, 135, 84, .25);
}
.btn-check:checked + .selectable-card.reguler::after {
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
.btn-check:checked + .selectable-card.premium {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
}
.btn-check:checked + .selectable-card.premium::after {
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
.btn-check:checked + .selectable-card.pay {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
}
.btn-check:checked + .selectable-card.pay::after {
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
.btn-check:focus + .selectable-card,
.btn-check:focus-visible + .selectable-card {
    box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .35);
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

            <form id="orderForm" action="{{ route('orders.preview') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                {{-- STEP 1 --}}
                <div class="wizard-step" data-step="1">
                    <h5 class="fw-bold mb-3">Pilih Paket</h5>

                    <div class="mb-2"><span class="badge bg-secondary">Reguler</span></div>
                    <div class="row g-3 mb-4">
                        @foreach ($packages as $key => $pkg)
                            @if ($pkg['category'] === 'Reguler')
                                <div class="col-md-3">
                                    <label class="w-100">
                                        <input type="radio" name="package_key" class="btn-check"
                                            value="{{ $key }}" required>
                                        <div class="card border-0 shadow-sm h-100 selectable-card reguler">
                                            <div class="card-body">
                                                <div class="small text-muted text-uppercase">{{ $pkg['label'] }}</div>
                                                <div class="h5 fw-bold mt-1">{{ number_format($pkg['price'], 0, ',', '.') }}
                                                </div>
                                                <small class="text-muted">{{ $pkg['category'] }}</small>
                                                <div class="mt-2"><span class="badge bg-light text-dark duration-badge"
                                                        data-for="{{ $key }}">Durasi: -</span></div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="mb-2"><span class="badge bg-secondary">Premium</span></div>
                    <div class="row g-3">
                        @foreach ($packages as $key => $pkg)
                            @if ($pkg['category'] === 'Premium')
                                <div class="col-md-3">
                                    <label class="w-100">
                                        <input type="radio" name="package_key" class="btn-check"
                                            value="{{ $key }}" required>
                                        <div class="card border-0 shadow-sm h-100 selectable-card premium">
                                            <div class="card-body">
                                                <div class="small text-muted text-uppercase">{{ $pkg['label'] }}</div>
                                                <div class="h5 fw-bold mt-1">{{ number_format($pkg['price'], 0, ',', '.') }}
                                                </div>
                                                <small class="text-muted">{{ $pkg['category'] }}</small>
                                                <div class="mt-2"><span class="badge bg-light text-dark duration-badge"
                                                        data-for="{{ $key }}">Durasi: -</span></div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        @endforeach
                    </div>

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

                    <div class="alert alert-light border mt-3 mb-0">
                        <div class="small mb-0">
                            <span class="me-2">Durasi paket: <strong id="durationInfo">-</strong> hari</span>
                            <span>Periode: <strong id="periodInfo">-</strong></span>
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
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Paket</dt>
                                <dd class="col-sm-8" id="summaryPackage">-</dd>
                                <dt class="col-sm-4">Kategori</dt>
                                <dd class="col-sm-8" id="summaryCategory">-</dd>
                                <dt class="col-sm-4">Periode</dt>
                                <dd class="col-sm-8"><span id="summaryDates">-</span> (<span id="summaryDays">0</span>
                                    hari)</dd>
                                <dt class="col-sm-4">Harga</dt>
                                <dd class="col-sm-8 fw-bold" id="summaryPrice">-</dd>
                            </dl>
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
                                        <div class="h6 mb-1">Transfer</div><small
                                            class="text-muted">BCA • BNI • BRI • Mandiri • OVO • GoPay • DANA</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="w-100">
                                <input type="radio" name="payment_method" class="btn-check" value="cod" required>
                                <div class="card border-0 shadow-sm h-100 selectable-card pay">
                                    <div class="card-body">
                                        <div class="h6 mb-1">COD</div><small class="text-muted">Bayar di tempat</small>
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
            </form>
        </div>
    </section>
  </div>

    @push('scripts')
        <script>
            const PACKAGES = @json($packages);

            // Tambah DURASI per paket (hari)
            const DURATION_DAYS = {
                reguler_harian: 1,
                reguler_mingguan: 7,
                reguler_bulanan: 30,
                reguler_3bulanan: 90,
                premium_ekspress: 1,
                premium_mingguan: 7,
                premium_bulanan: 30,
                premium_3bulanan: 90,
            };

            const rupiah = n => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(n);
            const toYMD = d => {
                const x = new Date(d);
                x.setHours(0, 0, 0, 0);
                const yyyy = x.getFullYear();
                const mm = String(x.getMonth() + 1).padStart(2, '0');
                const dd = String(x.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            };
            const addDays = (ymd, n) => {
                const d = new Date(ymd);
                d.setDate(d.getDate() + n);
                return toYMD(d);
            };

            (function() {
                const steps = Array.from(document.querySelectorAll('.wizard-step'));
                let current = 1;

                const progressEl = document.getElementById('wizardProgress');
                const nextBtns = document.querySelectorAll('[data-next]');
                const prevBtns = document.querySelectorAll('[data-prev]');
                const form = document.getElementById('orderForm');

                // Besok sebagai minimal start_date
                const today = new Date();
                const tomorrow = new Date(today);
                tomorrow.setDate(today.getDate() + 1);

                const startDate = document.getElementById('startDate');
                const endDate = document.getElementById('endDate');
                startDate.min = toYMD(tomorrow);
                endDate.readOnly = true;

                // tampilkan badge durasi di kartu
                document.querySelectorAll('.duration-badge').forEach(b => {
                    const key = b.getAttribute('data-for');
                    const days = DURATION_DAYS[key] ?? '-';
                    b.textContent = `Durasi: ${days} hari`;
                });

                // highlight kartu terpilih
                document.querySelectorAll('input[name="package_key"]').forEach(r => {
                    r.addEventListener('change', () => {
                        document.querySelectorAll('.selectable-card').forEach(c => c.classList.remove(
                            'border-primary', 'shadow'));
                        const card = r.closest('label').querySelector('.selectable-card');
                        if (card) {
                            card.classList.add('border-primary', 'shadow');
                        }
                        // kalau sudah pilih paket & sudah isi start_date, hitung ulang end_date
                        if (startDate.value) {
                            autoFillEndDate();
                            updatePeriodInfo();
                        }
                    });
                });

                function showStep(n) {
                    steps.forEach(s => s.classList.add('d-none'));
                    document.querySelector(`.wizard-step[data-step="${n}"]`).classList.remove('d-none');
                    current = n;
                    progressEl.style.width = (n * 25) + '%';
                }

                function getChosenKey() {
                    const el = document.querySelector('input[name="package_key"]:checked');
                    return el ? el.value : null;
                }

                function getDuration() {
                    const key = getChosenKey();
                    return key ? (DURATION_DAYS[key] || 0) : 0;
                }

                function validateStep1() {
                    return !!getChosenKey();
                }

                function autoFillEndDate() {
                    const dur = getDuration();
                    if (!startDate.value || !dur) return;
                    // end = start + dur - 1 hari
                    endDate.value = addDays(startDate.value, dur - 1);
                }

                function validateStep2() {
                    // start harus ada dan >= besok, end otomatis ada
                    if (!startDate.value) return false;
                    const startOk = new Date(startDate.value) >= new Date(toYMD(tomorrow));
                    const endOk = !!endDate.value;
                    return startOk && endOk;
                }

                function updatePeriodInfo() {
                    const dur = getDuration();
                    document.getElementById('durationInfo').textContent = dur || '-';
                    const periodText = (startDate.value && endDate.value) ? `${startDate.value} s/d ${endDate.value}` : '-';
                    document.getElementById('periodInfo').textContent = periodText;
                }

                function fillSummary() {
                    const chosen = getChosenKey();
                    const pkg = PACKAGES[chosen] || {};
                    const sd = startDate.value,
                        ed = endDate.value;
                    const days = getDuration(); // gunakan durasi paket

                    document.getElementById('summaryPackage').textContent = pkg.label || '-';
                    document.getElementById('summaryCategory').textContent = pkg.category || '-';
                    document.getElementById('summaryDates').textContent = (sd && ed) ? `${sd} s/d ${ed}` : '-';
                    document.getElementById('summaryDays').textContent = days || 0;
                    document.getElementById('summaryPrice').textContent = pkg.price ? rupiah(pkg.price) : '-';

                    // hidden (opsional; ikut terkirim)
                    document.getElementById('hidPackageLabel').value = pkg.label || '';
                    document.getElementById('hidPackageCategory').value = pkg.category || '';
                    document.getElementById('hidPackagePrice').value = pkg.price || '';
                }

                // perubahan start date → hitung end date otomatis
                startDate.addEventListener('change', () => {
                    // clamp ke minimal besok
                    if (new Date(startDate.value) < new Date(toYMD(tomorrow))) {
                        startDate.value = toYMD(tomorrow);
                    }
                    autoFillEndDate();
                    updatePeriodInfo();
                });

                nextBtns.forEach(btn => btn.addEventListener('click', () => {
                    if (current === 1) {
                        if (!validateStep1()) {
                            alert('Pilih salah satu paket terlebih dahulu.');
                            return;
                        }
                        showStep(2);
                        // Set default startDate = besok saat masuk step 2
                        if (!startDate.value) {
                            startDate.value = toYMD(tomorrow);
                            autoFillEndDate();
                            updatePeriodInfo();
                        }
                    } else if (current === 2) {
                        if (!validateStep2()) {
                            startDate.classList.toggle('is-invalid', !startDate.value || new Date(startDate
                                .value) < new Date(toYMD(tomorrow)));
                            endDate.classList.toggle('is-invalid', !endDate.value);
                            return;
                        }
                        startDate.classList.remove('is-invalid');
                        endDate.classList.remove('is-invalid');
                        fillSummary();
                        showStep(3);
                    } else if (current === 3) {
                        showStep(4);
                    }
                }));

                prevBtns.forEach(btn => btn.addEventListener('click', () => {
                    if (current > 1) showStep(current - 1);
                }));

                form.addEventListener('submit', e => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });

                // Toggle highlight untuk paket
                const packageRadios = document.querySelectorAll('input[name="package_key"]');

                function refreshPackageHighlights() {
                    document.querySelectorAll('label .selectable-card').forEach(c => c.classList.remove('active'));
                    const checked = document.querySelector('input[name="package_key"]:checked');
                    if (checked) {
                        const card = checked.closest('label').querySelector('.selectable-card');
                        if (card) card.classList.add('active');
                    }
                }
                packageRadios.forEach(r => r.addEventListener('change', () => {
                    refreshPackageHighlights();
                    // (opsional) hitung ulang tanggal:
                    if (startDate.value) {
                        autoFillEndDate();
                        updatePeriodInfo();
                    }
                }));
                // Inisialisasi saat load
                refreshPackageHighlights();

                // Toggle highlight untuk metode bayar (opsional)
                const payRadios = document.querySelectorAll('input[name="payment_method"]');

                function refreshPayHighlights() {
                    document.querySelectorAll('input[name="payment_method"]').forEach(r => {
                        const card = r.closest('label').querySelector('.selectable-card');
                        if (!card) return;
                        card.classList.toggle('active', r.checked);
                    });
                }
                payRadios.forEach(r => r.addEventListener('change', refreshPayHighlights));
                refreshPayHighlights();


                showStep(1);
            })();
        </script>
    @endpush
@endsection
