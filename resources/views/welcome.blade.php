<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>HeartFit — Hidup Sehat dengan Makanan Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-body-tertiary">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
                <img src="{{ asset('assets/img/favicon/heartfit_logo.png') }}" width="200px" alt="">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item"><a class="nav-link active" href="#hero">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#paket">Paket</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="/login" class="btn btn-outline-primary">Login</a>
                    <a href="{{ url('registrasi') }}" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section id="hero" class="py-5">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <span class="badge text-bg-light border">Makanan Sehat</span>
                    <h1 class="display-5 fw-bold mt-3">
                        Mulai Hari dengan <span class="text-primary">Pilihan Makanan Sehat</span>
                    </h1>
                    <p class="lead text-secondary">
                        HeartFit membantu Anda menemukan inspirasi menu sehat, menghitung kalori,
                        dan memantau kebiasaan makan agar jantung tetap kuat dan tubuh lebih bugar.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="/register" class="btn btn-primary btn-lg">
                            <i class="bi bi-basket"></i> Mulai Sekarang
                        </a>
                        <a href="#about" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-info-circle"></i> Pelajari Lebih Lanjut
                        </a>
                    </div>

                    <!-- mini stats -->
                    <div class="row g-3 mt-4">
                        <div class="col-6 col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="small text-secondary">Kalori Harian</div>
                                    <div class="h4 mb-0">1,850 kcal</div>
                                    <div class="small text-primary"><i class="bi bi-arrow-up-right"></i> Ideal</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="small text-secondary">Porsi Sayur</div>
                                    <div class="h4 mb-0">5x</div>
                                    <div class="small text-primary"><i class="bi bi-check-circle"></i> Sesuai</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="small text-secondary">Minum Air</div>
                                    <div class="h4 mb-0">2.5 L</div>
                                    <div class="small text-info"><i class="bi bi-droplet-half"></i> Cukup</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- gambar makanan sehat -->
                <div class="col-lg-6 text-center">
                    <img src="{{ asset('assets/img/logo/slhs.png') }}" class="img-fluid rounded"
                        alt="Healthy Food">
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Makanan -->
    <section id="paket" class="py-5 bg-white border-top">
        <div class="container">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary">Menu Makanan</h3>
                <p class="text-secondary mb-0">Menu Makanan Yang Akan Didapatkan</p>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3">
                        <img class="card-img-top object-fit-cover" style="height:400px;"  src="{{ asset('assets/img/menus/1.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Paket Diet Sehat Rendah Kalori</h5>
                            <p class="card-text">
                                Menu diet lengkap dengan porsi seimbang karbohidrat, protein, dan sayuran segar. Cocok untuk menjaga berat badan tetap ideal.
                            </p>
                            
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3">
                        <img class="card-img-top object-fit-cover" style="height:400px;"  src="{{ asset('assets/img/menus/2.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Lunch Box Diet Praktis</h5>
                            <p class="card-text">
                                Hidangan diet bernutrisi dengan buah dan sayuran segar, dikombinasikan dengan lauk sehat rendah lemak untuk energi sepanjang hari.
                            </p>
                            
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3">
                        <img class="card-img-top object-fit-cover" style="height:400px;"  src="{{ asset('assets/img/menus/3.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Healthy Diet Bento</h5>
                            <p class="card-text">
                                Sajian ala bento diet dengan porsi terkontrol, kaya serat dan protein, mendukung program diet tanpa rasa lapar.
                            </p>
                            
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3">
                        <img class="card-img-top object-fit-cover" style="height:400px;"  src="{{ asset('assets/img/menus/4.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Paket Makan Malam Diet</h5>
                            <p class="card-text">
                                Makanan diet rendah kalori dengan kombinasi lauk sehat, sayuran hijau, dan buah segar yang cocok untuk malam hari.
                            </p>
                            
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3">
                        <img class="card-img-top object-fit-cover" style="height:400px;"  src="{{ asset('assets/img/menus/5.jpg') }}" alt="Card image cap" />
                        <div class="card-body">
                            <h5 class="card-title">Diet Box Premium</h5>
                            <p class="card-text">
                                Menu diet premium dengan variasi makanan sehat, dibuat untuk menjaga metabolisme tubuh tetap optimal setiap hari.
                            </p>
                            
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <!-- PAKET (versi biru) -->
    <section id="paket" class="py-5 bg-white border-top">
        <div class="container">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary">HeartFit Diet Reguler</h3>
                <p class="text-secondary mb-0">Pilihan paket makan sehat untuk kebutuhan harian Anda.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Reguler</h5>
                            <div class="display-6 fw-bold mt-2">Rp 50.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">Menu seimbang</li>
                                <li class="list-group-item">Pilihan karbo sehat</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Mingguan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 400.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">4 hari × 2 kali makan</li>
                                <li class="list-group-item">8 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Bulanan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 1.180.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">12 hari × 2 kali makan</li>
                                <li class="list-group-item">24 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">3 Bulanan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 3.540.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">36 hari × 2 kali makan</li>
                                <li class="list-group-item">72 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Premium -->
            <div class="text-center my-5">
                <h3 class="fw-bold text-primary">HeartFit Diet Premium</h3>
                <p class="text-secondary mb-0">Pilihan premium untuk hasil lebih cepat & fleksibel.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Ekspress</h5>
                            <div class="display-6 fw-bold mt-2">Rp 170.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">2 kali makan (siang & malam)</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Mingguan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 650.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">4 hari × 2 kali makan</li>
                                <li class="list-group-item">8 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">Bulanan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 1.950.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">12 hari × 2 kali makan</li>
                                <li class="list-group-item">24 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold">3 Bulanan</h5>
                            <div class="display-6 fw-bold mt-2">Rp 5.830.000,-</div>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item">36 hari × 2 kali makan</li>
                                <li class="list-group-item">72 hari × 1 kali makan</li>
                            </ul>
                            <a href="#" class="btn btn-primary mt-3 w-100">Pesan Paket</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info border mt-4 mb-0" role="alert">
                <i class="bi bi-info-circle"></i>
                <span class="ms-2 small">
                    Harga dapat berubah sewaktu-waktu. Jadwal pengantaran & preferensi menu dapat diatur setelah
                    checkout.
                </span>
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="py-5 bg-white border-top">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold text-primary">Tentang HeartFit</h2>
                    <p class="text-secondary">
                        HeartFit bukan sekadar aplikasi kebugaran, tapi juga partner Anda untuk menjaga pola makan.
                        Kami menyediakan rekomendasi resep sehat, panduan nutrisi, serta reminder untuk pola hidup
                        seimbang.
                    </p>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start mb-2">
                            <i class="bi bi-check2-circle text-primary me-2"></i>
                            <span>Resep mudah & bergizi seimbang</span>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <i class="bi bi-check2-circle text-primary me-2"></i>
                            <span>Pencatatan kalori otomatis</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="bi bi-check2-circle text-primary me-2"></i>
                            <span>Rekomendasi menu sesuai kebutuhan</span>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <img src="https://picsum.photos/500/350?hospital" class="img-fluid rounded shadow-lg"
                        alt="About HeartFit">
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR -->
    <section id="fitur" class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary">Fitur Utama</h3>
                <p class="text-secondary mb-0">Makan lebih sehat, hidup lebih bertenaga.</p>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <i class="bi bi-basket2 fs-3 text-primary"></i>
                            <h5 class="mt-2">Inspirasi Resep</h5>
                            <p class="text-secondary mb-0">Ribuan ide makanan sehat praktis untuk setiap hari.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <i class="bi bi-bar-chart-line fs-3 text-primary"></i>
                            <h5 class="mt-2">Tracking Nutrisi</h5>
                            <p class="text-secondary mb-0">Hitung kalori, protein, karbohidrat, dan vitamin.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body">
                            <i class="bi bi-bell fs-3 text-primary"></i>
                            <h5 class="mt-2">Reminder Sehat</h5>
                            <p class="text-secondary mb-0">Ingatkan waktu makan, minum air, dan konsumsi buah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-top">
        <div class="container py-4">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-heart-fill text-primary"></i>
                    <span class="fw-semibold">HeartFit</span>
                </div>
                <div class="small text-secondary">© 2025 HeartFit. Semua hak dilindungi.</div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
