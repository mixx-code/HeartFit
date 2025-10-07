<!doctype html>
<html lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets') }}/"
  data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>@yield('title','Dashboard')</title>
  <meta name="description" content="" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Favicon --}}
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/heartfit_logo.png') }}" />

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

  {{-- Icons --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

  {{-- Core CSS Sneat --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

  {{-- Vendors CSS --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

  {{-- Tambahkan baris ini --}}
  @stack('styles')

  {{-- Helpers & Config (harus di head, setelah core styles) --}}
  <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('assets/js/config.js') }}"></script>
</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      @include('partials.sidebar')
      <div class="layout-page">
        @include('partials.navbar')
        <div class="content-wrapper">
          @yield('content')
        </div>
      </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>

  {{-- === JS: URUTAN BENAR === --}}
  <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

  {{-- Menu (hapus baris ini jika file-nya tidak ada) --}}
  <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

  {{-- ApexCharts harus sebelum dashboards-analytics.js --}}
  <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

  {{-- Main init Sneat --}}
  <script src="{{ asset('assets/js/main.js') }}"></script>

  {{-- Page JS yang gambar chart --}}
  <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>

  <script async defer src="https://buttons.github.io/buttons.js"></script>
  @stack('scripts')
</body>
</html>
