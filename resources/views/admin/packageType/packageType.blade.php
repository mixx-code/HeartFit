@extends('layouts.app')

@section('title', 'Package Types')
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
      {{-- Judul di kiri --}}
      <h5 class="mb-0">Table Package Types</h5>

      {{-- Form + tombol di kanan --}}
      <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
        {{-- Search + Page size (GET) --}}
        <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('admin.packageType') }}">
          <div class="input-group" style="min-width: 280px;">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Cari package type ...">
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
            <a href="{{ route('admin.package-types.index', ['per_page' => request('per_page', $perPage)]) }}"
               class="btn btn-outline-secondary">Reset</a>
          @endif
        </form>

        {{-- Tombol Tambah Data --}}
        <a class="btn btn-success" href="{{ route('admin.packageType.addPackageType') }}">
          <i class="bi bi-plus-circle"></i> Tambah Package Type
        </a>
      </div>
    </div>

    <div class="table-responsive text-nowrap" style="min-height: 400px">
      <table class="table">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Package Type</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th style="width: 80px;">Actions</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @forelse($packageTypes as $t)
            <tr>
              <td>{{ $t->id }}</td>
              <td>{{ $t->packageType }}</td>
              <td>{{ $t->created_at }}</td>
              <td>{{ $t->updated_at }}</td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="">
                      <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>
                    {{-- <a class="dropdown-item" href="{{ route('admin.package-types.edit', $t->id) }}">
                      <i class="bx bx-edit-alt me-1"></i> Edit
                    </a> --}}

                    {{-- DELETE --}}
                    {{-- <form action="{{ route('admin.package-types.destroy', $t->id) }}" method="POST"
                          onsubmit="return confirm('Yakin hapus {{ $t->package_type }}?');"> --}}
                    <form action="" method="POST"
                          onsubmit="return confirm('Yakin hapus {{ $t->package_type }}?');">
                      @csrf
                      @method('DELETE')
                      {{-- pertahankan query agar kembali ke kondisi saat ini --}}
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
              <td colspan="5" class="text-center text-muted">
                Tidak ada data{{ request('q') ? ' untuk pencarian ini' : '' }}.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
      <div class="small text-muted">
        @if($packageTypes->total() > 0)
          Menampilkan {{ $packageTypes->firstItem() }}–{{ $packageTypes->lastItem() }} dari {{ $packageTypes->total() }} data
        @else
          Menampilkan 0–0 dari 0 data
        @endif
      </div>
      {{ $packageTypes->appends(request()->query())->links('pagination::bootstrap-5-ellipses') }}
    </div>
  </div>
</div>
@endsection
