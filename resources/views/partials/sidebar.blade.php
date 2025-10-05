<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('welcome') }}" class="app-brand-link">
      {{-- ... logo kamu tetap ... --}}
      <span class="app-brand-text demo menu-text fw-bolder ms-2">Sneat</span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  @php
    $isAuth = auth()->check();
    $role   = $isAuth ? (auth()->user()->role ?? null) : null;
  @endphp

  <ul class="menu-inner py-1">
    {{-- Dashboard --}}
    <li class="menu-item {{ request()->routeIs('dashboard.admin') || request()->routeIs('dashboard.customer') ? 'active' : '' }}">
      @if($isAuth && $role === 'admin')
        <a href="{{ route('dashboard.admin') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div data-i18n="Analytics">Dashboard</div>
        </a>
      @else
        <a href="{{ route('dashboard.customer') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div data-i18n="Analytics">Dashboard</div>
        </a>
      @endif
    </li>

    {{-- Hanya untuk ADMIN --}}
    @if($isAuth && $role === 'admin')
      <li class="menu-header small text-uppercase"><span class="menu-header-text">Data Users</span></li>

      <li class="menu-item {{ request()->is('petugas*') || request()->is('admin*') || request()->is('customers*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-table"></i>
          <div data-i18n="data">Data User</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('petugas.*') ? 'active' : '' }}">
            <a href="{{ route('petugas.index') }}" class="menu-link">
              <div data-i18n="Without menu">Petugas/Admin</div>
            </a>
          </li>
          <li class="menu-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <a href="{{ route('admin.data.customers') }}" class="menu-link">
              <div data-i18n="Without menu">Customers</div>
            </a>
          </li>
        </ul>
      </li>

      <li class="menu-header small text-uppercase"><span class="menu-header-text">Add Products</span></li>
      <li class="menu-item {{ request()->routeIs('admin.products.add') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-detail"></i>
          <div data-i18n="Form Elements">Add Products</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->routeIs('admin.products.add') ? 'active' : '' }}">
            <a href="{{ route('admin.products.add') }}" class="menu-link">
              <div data-i18n="Input groups">Add Products</div>
            </a>
          </li>
        </ul>
      </li>
    @endif

    {{-- Transaksi (umum / sesuai kebutuhan) --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Orders</span></li>
    <li class="menu-item {{ request()->is('transaksi*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-detail"></i>
        <div data-i18n="Form Elements">Orders</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('riwayatTransaksi') ? 'active' : '' }}">
          <a href="{{ route('orders.index') }}" class="menu-link">
            <div data-i18n="Input groups">List Orders</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-header small text-uppercase"><span class="menu-header-text">Transaksi</span></li>
    <li class="menu-item {{ request()->is('transaksi*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-detail"></i>
        <div data-i18n="Form Elements">Transaksi</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->is('riwayatTransaksi') ? 'active' : '' }}">
          <a href="{{ url('/riwayatTransaksi') }}" class="menu-link">
            <div data-i18n="Input groups">Riwayat Transaksi</div>
          </a>
        </li>
        <li class="menu-item {{ request()->is('statusTransaksi') ? 'active' : '' }}">
          <a href="{{ url('/statusTransaksi') }}" class="menu-link">
            <div data-i18n="Input groups">Status Transaksi</div>
          </a>
        </li>
      </ul>
    </li>
    
  </ul>
</aside>
