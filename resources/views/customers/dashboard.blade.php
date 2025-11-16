@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl container-p-y">
        <div class="row g-4">



            {{-- Kirim data dari dashboard --}}
            <x-delivery-status :row="$items->first()" :date="$date" :steps="['PENDING', 'PROSES', 'KIRIM', 'SAMPAI']" />


            {{-- CTA PESAN (SELALU TAMPIL) --}}
            <x-btn-create-order title="Mulai Langganan Sekarang"
                desc="Pilih paket favoritmu dan atur jadwal pengantaran sesuai kebutuhan." route="orders.create"
                btn="Buka Halaman Pemesanan" flash="warning" />

            {{-- <x-diet-history :items="$dietHistory" title="Riwayat Diet Saya" :limit="10" /> --}}
            <x-diet-history />

            <div id="paket" class="col-12">
                <x-package-section title="HeartFit Diet Reguler"
                    subtitle="Pilihan paket makan sehat untuk kebutuhan harian Anda." />
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
