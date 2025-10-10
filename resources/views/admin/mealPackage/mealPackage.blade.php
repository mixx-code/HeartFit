@extends('layouts.app')

@section('title', 'Meal Packages')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">

    {{-- FLASH MESSAGE --}}
    @if(session('status'))
      <div class="alert alert-success m-3 mb-0">
        {{ session('status') }}
      </div>
    @endif

    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
      <h5 class="mb-0">Table Meal Packages</h5>

      <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
        {{-- Search + Page size (GET) --}}
        <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('admin.mealPackage') }}">
          <div class="input-group" style="min-width: 280px;">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Cari jenis paket / batch ...">
          </div>
          <div class="input-group" style="max-width: 160px;">
            <span class="input-group-text">Rows</span>
            <select name="per_page" class="form-select" onchange="this.form.submit()">
              @foreach ([5, 10, 15, 20] as $size)
                <option value="{{ $size }}" {{ (int) request('per_page', $perPage) === $size ? 'selected' : '' }}>
                  {{ $size }}
                </option>
              @endforeach
            </select>
          </div>
          <button class="btn btn-primary" type="submit">Search</button>

          @if (request('q'))
            <a href="{{ route('admin.mealPackage', ['per_page' => request('per_page', $perPage)]) }}"
               class="btn btn-outline-secondary">Reset</a>
          @endif
        </form>

        {{-- Tambah --}}
        <a class="btn btn-success" href="{{ route('admin.mealPackage.addMealPackage') }}">
          <i class="bi bi-plus-circle"></i> Tambah Meal Package
        </a>
      </div>
    </div>

    <div class="table-responsive text-nowrap" style="min-height: 400px">
      <table class="table">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Nama Meal Package</th>
            <th>Batch</th>
            <th>Jenis Paket</th>
            <th>Porsi Paket</th>
            <th>Total Hari</th>
            <th>Detail Paket</th>
            <th>Price</th>
            <th>Package Type</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th style="width: 80px;">Actions</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @forelse($packages as $p)
            <tr>
              <td>{{ $p->id }}</td>
              <td>{{ $p->nama_meal_package }}</td>
              <td>{{ $p->batch ?? '—' }}</td>
              <td class="text-capitalize">{{ $p->jenis_paket }}</td>
              <td>{{ $p->porsi_paket }}</td>
              <td>{{ $p->total_hari }}</td>
              <td>{{ $p->detail_paket }}</td>
              <td>Rp. {{ number_format($p->price, 0, ',', '.') }}</td>
              <td>{{ optional($p->packageType)->packageType ?? '—' }}</td>
              <td>{{ $p->created_at }}</td>
              <td>{{ $p->updated_at }}</td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.mealPackage.edit', $p->id) }}">
                      <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>

                    <form 
                    action="{{ route('admin.mealPackage.delete', $p->id) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin hapus paket ini?');">
                      @csrf
                      @method('DELETE')
                      <input type="hidden" name="q" value="{{ request('q') }}">
                      <input type="hidden" name="per_page" value="{{ request('per_page', $perPage) }}">
                      <input type="hidden" name="page" value="{{ request('page') }}">
                      <button type="submit" class="dropdown-item text-danger">
                        <i class="bx bx-trash me-1"></i> Delete
                      </button>
                    </form>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-muted">
                Tidak ada data{{ request('q') ? ' untuk pencarian ini' : '' }}.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
      <div class="small text-muted">
        @if($packages->total() > 0)
          Menampilkan {{ $packages->firstItem() }}–{{ $packages->lastItem() }} dari {{ $packages->total() }} data
        @else
          Menampilkan 0–0 dari 0 data
        @endif
      </div>
      {{ $packages->appends(request()->query())->links('pagination::bootstrap-5-ellipses') }}
    </div>
  </div>
</div>
@endsection
