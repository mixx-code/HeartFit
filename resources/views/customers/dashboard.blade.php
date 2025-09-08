@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-4">

    {{-- CTA PESAN (kartu info cepat) --}}
    <div class="col-md-6 col-lg-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h5 class="card-title mb-2">Pesan HeartFit dengan Mudah</h5>
          <p class="card-text text-secondary mb-3">
            Ingin mulai langganan makan sehat? <strong>Kalau mau pesan, klik di sini</strong> untuk memilih paket dan atur jadwal pengantaran sesuai kebutuhanmu.
          </p>
          {{-- Ganti route ke rute pemesanan milikmu bila sudah tersedia --}}
          <a href="{{ url('/orders/create') }}" class="btn btn-primary">
            Pesan Sekarang
          </a>
        </div>
      </div>
    </div>

    {{-- KONTEN PAKET --}}
    <div id="paket" class="col-12">
      <section class="py-5 bg-white border rounded-3">
        <div class="container">

          {{-- Header Reguler --}}
          <div class="text-center mb-4">
            <h3 class="fw-bold text-primary mb-1">HeartFit Diet Reguler</h3>
            <p class="text-secondary mb-0">Pilihan paket makan sehat untuk kebutuhan harian Anda.</p>
          </div>

          <div class="row g-3">
            <div class="col-md-3">
              <div class="card border-0 shadow-lg h-100">
                <div class="card-body d-flex flex-column">
                  <h6 class="text-uppercase text-secondary mb-1">Reguler</h6>
                  <div class="h3 fw-bold mt-1">Rp 50.000,-</div>
                  <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">Menu seimbang</li>
                    <li class="list-group-item">Pilihan karbo sehat</li>
                  </ul>
                  <a href="" class="btn btn-outline-primary mt-3 w-100">Pesan Paket</a>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card border-0 shadow-lg h-100">
                <div class="card-body d-flex flex-column">
                  <h6 class="text-uppercase text-secondary mb-1">Mingguan</h6>
                  <div class="h3 fw-bold mt-1">Rp 400.000,-</div>
                  <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">4 hari × 2 kali makan</li>
                    <li class="list-group-item">8 hari × 1 kali makan</li>
                  </ul>
                  <a href="" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card border-0 shadow-lg h-100">
                <div class="card-body d-flex flex-column">
                  <h6 class="text-uppercase text-secondary mb-1">Bulanan</h6>
                  <div class="h3 fw-bold mt-1">Rp 1.180.000,-</div>
                  <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">12 hari × 2 kali makan</li>
                    <li class="list-group-item">24 hari × 1 kali makan</li>
                  </ul>
                  <a href="" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card border-0 shadow-lg h-100">
                <div class="card-body d-flex flex-column">
                  <h6 class="text-uppercase text-secondary mb-1">3 Bulanan</h6>
                  <div class="h3 fw-bold mt-1">Rp 3.540.000,-</div>
                  <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">36 hari × 2 kali makan</li>
                    <li class="list-group-item">72 hari × 1 kali makan</li>
                  </ul>
                  <a href="" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                </div>
              </div>
            </div>
          </div>

          {{-- Header Premium --}}
          <div class="text-center my-5">
            <h3 class="fw-bold text-primary mb-1">HeartFit Diet Premium</h3>
            <p class="text-secondary mb-0">Pilihan premium untuk hasil lebih cepat & fleksibel.</p>
          </div>

          <div class="row g-3">
            <div class="col-md-3">
              <div class="card border-0 shadow-lg h-100">
                <div class="card-body d-flex flex-column">
                  <h6 class="text-uppercase text-secondary mb-1">Ekspress</h6>
                  <div class="h3 fw-bold mt-1">Rp 170.000,-</div>
                  <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">2 kali makan (siang & malam)</li>
                  </ul>
                  <a href="" class="btn btn-outline-primary mt-3 w-100">Pesan Paket</a>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card border-0 shadow-lg h-100">
                <div class="card-body d-flex flex-column">
                  <h6 class="text-uppercase text-secondary mb-1">Mingguan</h6>
                  <div class="h3 fw-bold mt-1">Rp 650.000,-</div>
                  <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">4 hari × 2 kali makan</li>
                    <li class="list-group-item">8 hari × 1 kali makan</li>
                  </ul>
                  <a href="" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card border-0 shadow-lg h-100">
                <div class="card-body d-flex flex-column">
                  <h6 class="text-uppercase text-secondary mb-1">Bulanan</h6>
                  <div class="h3 fw-bold mt-1">Rp 1.950.000,-</div>
                  <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">12 hari × 2 kali makan</li>
                    <li class="list-group-item">24 hari × 1 kali makan</li>
                  </ul>
                  <a href="" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card border-0 shadow-lg h-100">
                <div class="card-body d-flex flex-column">
                  <h6 class="text-uppercase text-secondary mb-1">3 Bulanan</h6>
                  <div class="h3 fw-bold mt-1">Rp 5.830.000,-</div>
                  <ul class="list-group list-group-flush mt-3">
                    <li class="list-group-item">36 hari × 2 kali makan</li>
                    <li class="list-group-item">72 hari × 1 kali makan</li>
                  </ul>
                  <a href="" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                </div>
              </div>
            </div>
          </div>

          {{-- Info harga & jadwal --}}
          <div class="alert alert-info border mt-4 mb-0" role="alert">
            <i class="bi bi-info-circle"></i>
            <span class="ms-2 small">
              Harga dapat berubah sewaktu-waktu. Jadwal pengantaran & preferensi menu dapat diatur setelah checkout.
            </span>
          </div>

          {{-- ANCHOR untuk tombol "Pesan Paket" --}}
          <div id="pesan" class="mt-5 text-center">
            <h5 class="fw-bold mb-2">Siap mulai makan sehat?</h5>
            <p class="text-secondary mb-3">Klik tombol di bawah ini untuk membuat pesanan pertama Anda.</p>
            <a href="" class="btn btn-lg btn-success">Buat Pesanan</a>
          </div>

        </div>
      </section>
    </div>

    {{-- Kalender --}}
    <div class="col-12">
        <div class="py-5 bg-white border rounded-3">
            @include('customers.kalender')
        </div>
    </div>

  </div>
</div>
@endsection
