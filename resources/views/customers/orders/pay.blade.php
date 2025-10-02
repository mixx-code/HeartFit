@extends('layouts.app')
@section('title', 'Bayar Pesanan')

@section('content')
<div class="container py-5">
  <h3 class="fw-bold text-primary mb-3">Pembayaran</h3>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h5 class="fw-semibold mb-3">Rincian Pesanan</h5>
          <dl class="row mb-0">
            <dt class="col-sm-4">Nomor Order</dt>
            <dd class="col-sm-8">{{ $order->order_number }}</dd>

            <dt class="col-sm-4">Paket</dt>
            <dd class="col-sm-8">{{ $order->package_label }} ({{ $order->package_category }})</dd>

            <dt class="col-sm-4">Periode</dt>
            <dd class="col-sm-8">{{ $order->start_date->toDateString() }} s/d {{ $order->end_date->toDateString() }} ({{ $order->days }} hari)</dd>

            <dt class="col-sm-4">Total</dt>
            <dd class="col-sm-8">Rp {{ number_format($order->amount_total ?? $order->package_price,0,',','.') }}</dd>

            <dt class="col-sm-4">Metode</dt>
            <dd class="col-sm-8">{{ strtoupper(str_replace('_',' ',$order->payment_method)) }}</dd>

            <dt class="col-sm-4">Status</dt>
            <dd class="col-sm-8">
              <span class="badge {{ $order->status === 'UNPAID' ? 'bg-warning' : 'bg-success' }}">{{ $order->status }}</span>
            </dd>
          </dl>
        </div>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ route('orders.create') }}" class="btn btn-outline-secondary">Buat Order Baru</a>

        @if($order->status === 'UNPAID')
          <button id="btnPay" class="btn btn-primary">Bayar Sekarang</button>
        @else
          <a href="{{ route('orders.finish', $order) }}" class="btn btn-success">Lihat Halaman Selesai</a>
        @endif
      </div>
    </div>

    <div class="col-lg-5">
      <div class="alert alert-info small">
        <div class="fw-semibold mb-1">Catatan:</div>
        Status final pembayaran akan disinkronkan melalui notifikasi server (webhook) dari Midtrans.
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
{{-- pay.blade.php --}}
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function postSnapResult(payload) {
  try {
    await fetch(@json(route('orders.snap_result', $order)), {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify(payload)
    });
  } catch(e) { console.error('snapResult error', e); }
}

async function confirmToServer(midtransOrderId) {
  try {
    const res = await fetch(@json(route('orders.confirm', $order)), {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
      body: JSON.stringify({ midtrans_order_id: midtransOrderId })
    });
    return await res.json();
  } catch(e) {
    console.error('confirm error', e);
    return { ok:false };
  }
}

document.getElementById('btnPay')?.addEventListener('click', function() {
  window.snap.pay(@json($snapToken), {
    onSuccess: async (result) => {
      console.log('onSuccess', result);
      await postSnapResult({ hook:'success', result });
      await confirmToServer(result?.order_id);       // verifikasi server-to-server
      window.location.href = @json(route('orders.finish', $order));
    },
    onPending: async (result) => {
      console.log('onPending', result);
      await postSnapResult({ hook:'pending', result });
      await confirmToServer(result?.order_id);       // sinkronkan walau pending
      alert('Transaksi pending. Silakan selesaikan pembayaran Anda.');
    },
    onError: async (result) => {
      console.log('onError', result);
      await postSnapResult({ hook:'error', result });
      alert('Terjadi kesalahan saat memproses pembayaran.');
    },
    onClose: async () => {
      console.log('onClose');
      await postSnapResult({ hook:'close' });
    }
  });
});


</script>

@endpush

