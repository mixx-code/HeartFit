<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('welcome') }}" class="app-brand-link">
            {{-- ... logo kamu tetap ... --}}
            <img src="{{ asset('assets/img/favicon/heartfit_logo.png') }}" width="200px" alt="">
            {{-- <span class="app-brand-text demo menu-text fw-bolder ms-2">heartfit</span> --}}
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $isAuth = auth()->check();
        $role = $isAuth ? auth()->user()->role ?? null : null;
    @endphp

    <ul class="menu-inner py-1">
        {{-- Dashboard --}}
        <li
            class="menu-item {{ request()->routeIs('dashboard.admin') || request()->routeIs('dashboard.customer') ? 'active' : '' }}">
            @if ($isAuth && $role === 'admin' || $role === 'ahli_gizi' || $role === 'bendahara' || $role === 'medical_record')
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

        {{-- ========================= --}}
        {{-- ADMIN ONLY                --}}
        {{-- ========================= --}}
        @if ($isAuth && $role === 'admin')
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Data Users</span></li>

            <li
                class="menu-item {{ request()->routeIs('admin.data.petugas*', 'admin.data.customers*', 'admin.data.customer*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-table"></i>
                    <div data-i18n="data">Data User</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.data.petugas*') ? 'active' : '' }}">
                        <a href="{{ route('admin.data.petugas') }}" class="menu-link">
                            <div data-i18n="Without menu">Petugas/Admin</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->routeIs('admin.data.customers*', 'admin.data.customer*') ? 'active' : '' }}">
                        <a href="{{ route('admin.data.customers') }}" class="menu-link">
                            <div data-i18n="Without menu">Customers</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header small text-uppercase"><span class="menu-header-text">Package/Menu</span></li>
            <li
                class="menu-item {{ request()->routeIs('admin.packageType*', 'admin.mealPackage*', 'admin.menuMakanan*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-detail"></i>
                    <div data-i18n="Form Elements">Package/Menu</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.packageType*') ? 'active' : '' }}">
                        <a href="{{ route('admin.packageType') }}" class="menu-link">
                            <div data-i18n="Input groups">Package Type</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.mealPackage*') ? 'active' : '' }}">
                        <a href="{{ route('admin.mealPackage') }}" class="menu-link">
                            <div data-i18n="Input groups">Meal Package</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.menuMakanan*') ? 'active' : '' }}">
                        <a href="{{ route('admin.menuMakanan') }}" class="menu-link">
                            <div data-i18n="Input groups">Menu Makanan</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header small text-uppercase"><span class="menu-header-text">Orders</span></li>
            <li class="menu-item {{ request()->routeIs('admin.orders.index') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-detail"></i>
                    <div data-i18n="Form Elements">Orders</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}" class="menu-link">
                            <div data-i18n="Input groups">List Orders</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif


        {{-- ========================= --}}
        {{-- MEDICAL RECORD ONLY       --}}
        {{-- ========================= --}}
        @if ($isAuth && $role === 'medical_record')
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Data Customers</span></li>
            <li
                class="menu-item {{ request()->routeIs('admin.data.customers*', 'admin.data.customer*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-table"></i>
                    <div data-i18n="data">Customers</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->routeIs('admin.data.customers*', 'admin.data.customer*') ? 'active' : '' }}">
                        <a href="{{ route('admin.data.customers') }}" class="menu-link">
                            <div data-i18n="Without menu">List & Detail</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif


        {{-- ========================= --}}
        {{-- BENDAHARA ONLY            --}}
        {{-- ========================= --}}
        @if ($isAuth && $role === 'bendahara')
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Orders</span></li>
            <li class="menu-item {{ request()->routeIs('admin.orders.index') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-receipt"></i>
                    <div data-i18n="Form Elements">Orders</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}" class="menu-link">
                            <div data-i18n="Input groups">List Orders</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif


        {{-- ========================= --}}
        {{-- AHLI GIZI ONLY            --}}
        {{-- ========================= --}}
        @if ($isAuth && $role === 'ahli_gizi')
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Menu Makanan</span></li>
            <li class="menu-item {{ request()->routeIs('admin.menuMakanan*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-restaurant"></i>
                    <div data-i18n="Form Elements">Menu Makanan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.menuMakanan') ? 'active' : '' }}">
                        <a href="{{ route('admin.menuMakanan') }}" class="menu-link">
                            <div data-i18n="Input groups">List Menu</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->routeIs('admin.menuMakanan.addMenuMakanan') ? 'active' : '' }}">
                        <a href="{{ route('admin.menuMakanan.addMenuMakanan') }}" class="menu-link">
                            <div data-i18n="Input groups">Tambah Menu</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif


        {{-- ========================= --}}
        {{-- CUSTOMER ONLY             --}}
        {{-- ========================= --}}
        @if ($isAuth && $role === 'customer')
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Orders</span></li>
            <li class="menu-item {{ request()->routeIs('customer.orders.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-cart"></i>
                    <div data-i18n="Form Elements">Orders</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('customer.orders.index') ? 'active' : '' }}">
                        <a href="{{ route('customer.orders.index') }}" class="menu-link">
                            <div data-i18n="Input groups">List Orders</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Profil</span>
            </li>

            <li class="menu-item {{ request()->routeIs('customers.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="Form Elements">Data Saya</div>
                </a>
                <ul class="menu-sub">
                    {{-- Jika belum punya detail, tampilkan tombol "Lengkapi Profil" --}}
                    @if (!$userDetail)
                        <li class="menu-item {{ request()->routeIs('customers.create') ? 'active' : '' }}">
                            <a href="{{ route('customers.create') }}" class="menu-link">
                                <div data-i18n="Input groups">Lengkapi Profil</div>
                            </a>
                        </li>
                    @else
                        {{-- Kalau sudah punya detail_user, arahkan ke halaman detail --}}
                        <li class="menu-item {{ request()->routeIs('customer.data.customer.detail') ? 'active' : '' }}">
                            <a href="{{ route('customer.data.customer.detail', $userDetail->id) }}" class="menu-link">
                                <div data-i18n="Input groups">Lihat Profil</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

        @endif



    </ul>
</aside>
