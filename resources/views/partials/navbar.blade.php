<nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  @if (Auth::check())
                  <div class="d-flex align-items-center gap-2">
                    <h5 class="card-header mb-0">{{ Auth::user()->name }}</h5>
                    {{-- Label Periode Langganan Aktif --}}
                    @php
                      // Cek apakah customer memiliki order PAID (akan datang atau sedang aktif)
                      $hasActiveDelivery = false;
                      $activeOrder = null;
                      if (Auth::user()->role === 'customer') {
                        try {
                          $activeOrder = \App\Models\Order::where('user_id', Auth::id())
                              ->where('status', 'PAID')
                              ->whereDate('end_date', '>=', \Carbon\Carbon::now())
                              ->first();
                          $hasActiveDelivery = !empty($activeOrder);
                        } catch (\Exception $e) {
                          $hasActiveDelivery = false;
                        }
                      }
                    @endphp
                    @if($hasActiveDelivery && $activeOrder)
                      @php
                        $endDate = \Carbon\Carbon::parse($activeOrder->end_date)->locale('id')->isoFormat('D MMMM YYYY');
                      @endphp
                      <span class="badge bg-info text-white" style="font-size: 11px;">Periode Langganan sampai {{ $endDate }}</span>
                    @endif
                  </div>
                  @endif
                  <i class="d-none bx bx-search fs-4 lh-0"></i>
                  <input
                    type="text"
                    class="form-control border-0 shadow-none"
                    placeholder="Search..."
                    aria-label="Search..."
                    disabled
                    hidden
                  />
                </div>
              </div>
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <li class="d-none nav-item lh-1 me-3">
                  <a
                    class="github-button"
                    href="https://github.com/themeselection/sneat-html-admin-template-free"
                    data-icon="octicon-star"
                    data-size="large"
                    data-show-count="true"
                    aria-label="Star themeselection/sneat-html-admin-template-free on GitHub"
                    >Star</a
                  >
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <span class="avatar-initial rounded-circle bg-label-primary" style="font-size:14px;font-weight:600;">
                        @auth{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(Auth::user()->name, ' '), 1, 1)) }}@endauth
                      </span>
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <span class="avatar-initial rounded-circle bg-label-primary" style="font-size:14px;font-weight:600;">
                                @auth{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(Auth::user()->name, ' '), 1, 1)) }}@endauth
                              </span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            @if (Auth::check())
                            <span class="fw-semibold d-block">{{ Auth::user()->name }}</span>
                            <small class="text-muted">{{ Auth::user()->role }}</small>
                            @endif
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item">
                          <i class="bx bx-power-off me-2"></i>
                          <span class="align-middle">Log Out</span>
                        </button>
                      </form>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>