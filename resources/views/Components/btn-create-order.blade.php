@props([
  'title'   => 'Pesan HeartFit dengan Mudah',
  'desc'    => 'Ingin mulai langganan makan sehat? Kalau mau pesan, klik di sini untuk memilih paket dan atur jadwal pengantaran sesuai kebutuhanmu.',
  'route'   => 'orders.create',   // nama route
  'btn'     => 'Pesan Sekarang',  // label tombol
  'flash'   => 'warning',         // key session flash (optional)
])

<div class="col-md-6 col-lg-4">
  <div class="card shadow-sm border-0 h-100">
    <div class="card-body">
      <h5 class="card-title mb-2">{{ $title }}</h5>

      <p class="card-text text-secondary mb-3">
        {!! nl2br(e($desc)) !!}
      </p>

      @if ($flash && session($flash))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          {{ session($flash) }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <a href="{{ route($route) }}" class="btn btn-primary">{{ $btn }}</a>
    </div>
  </div>
</div>
