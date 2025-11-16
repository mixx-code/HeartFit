<section class="py-5 bg-white border rounded-3">
    <div class="container">
        {{-- Header --}}
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary mb-1">{{ $title }}</h3>
            <p class="text-secondary mb-0">{{ $subtitle }}</p>
        </div>

        {{-- Cards --}}
        <div class="row g-3">
            @foreach ($packages as $pkg)
                @php
                    $label = $pkg['label'] ?? '-';
                    $price = $pkg['price'] ?? 0;
                    $features = $pkg['features'] ?? [];
                    $btnText = data_get($pkg, 'button.text', 'Pesan Paket');
                    $btnVar = data_get($pkg, 'button.variant', 'primary'); // primary | outline-primary | success | dll
                    $href = $pkg['href'] ?? ($orderRoute ? route($orderRoute) : '#');
                @endphp

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card border-0 shadow-lg h-100">
                        <div class="card-body d-flex flex-column">
                            <h6 class="text-uppercase text-secondary mb-1">{{ $label }}</h6>
                            <div class="h3 fw-bold mt-1">
                                Rp {{ number_format((float) $price, 0, ',', '.') }},-
                            </div>

                            @if (!empty($features))
                                <ul class="list-group list-group-flush mt-3">
                                    @foreach ($features as $f)
                                        <li class="list-group-item">{{ $f }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <a href="{{ $href }}" class="btn btn-{{ $btnVar }} mt-3 w-100">
                                {{ $btnText }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
