@extends('layouts.app')

@section('title', 'Menu Makanans')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">

            {{-- FLASH MESSAGE --}}
            @if (session('status'))
                <div class="alert alert-success m-3 mb-0">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <h5 class="mb-0">Table Menu Makanans</h5>

                <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                    {{-- Search + Page size (GET) --}}
                    <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('admin.menuMakanan') }}">
                        <div class="input-group" style="min-width: 280px;">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Cari nama menu / batch ...">
                        </div>
                        <div class="input-group" style="max-width: 160px;">
                            <span class="input-group-text">Rows</span>
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach ([5, 10, 15, 20] as $size)
                                    <option value="{{ $size }}"
                                        {{ (int) request('per_page', $perPage ?? 10) === $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Search</button>

                        @if (request('q'))
                            <a href="{{ route('admin.menuMakanan', ['per_page' => request('per_page', $perPage ?? 10)]) }}"
                                class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </form>

                    {{-- Tambah --}}
                    <a class="btn btn-success" href="{{ route('admin.menuMakanan.addMenuMakanan') }}">
                        <i class="bi bi-plus-circle"></i> Tambah Menu Makanan
                    </a>
                </div>
            </div>

            <div class="table-responsive text-nowrap" style="min-height: 400px">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama Menu</th>
                            <th>Batch</th>
                            <th>Serve Days</th>
                            <th>Spec Menu</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th style="width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($menus as $m)
                            <tr>
                                <td>{{ $m->id }}</td>
                                <td>{{ $m->nama_menu }}</td>
                                <td>{{ $m->batch ?? '—' }}</td>

                                {{-- Serve Days lebih netral + ada tulisan "Tanggal" --}}
                                <td>
                                    @php
                                        $days = is_array($m->serve_days)
                                            ? $m->serve_days
                                            : (json_decode($m->serve_days ?? '[]', true) ?:
                                            []);
                                    @endphp
                                    @forelse ($days as $d)
                                        <span class="badge bg-light text-dark border me-1 mb-1">Tanggal
                                            {{ $d }}</span>
                                    @empty
                                        <span class="text-muted">—</span>
                                    @endforelse
                                </td>

                                {{-- Spec Menu: tampilan list sederhana, minim warna --}}
                                <td>
                                    @php
                                        $spec = is_array($m->spec_menu)
                                            ? $m->spec_menu
                                            : (json_decode($m->spec_menu ?? '{}', true) ?:
                                            []);
                                        $siang = $spec['Makan Siang'] ?? [];
                                        $malam = $spec['Makan Malam'] ?? [];
                                    @endphp

                                    <div class="spec-wrap">
                                        <div class="spec-block">
                                            <div class="spec-title">Makan Siang</div>
                                            @if (count($siang))
                                                <ul class="spec-list">
                                                    @foreach ($siang as $item)
                                                        <li>{{ $item }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </div>

                                        <div class="spec-block">
                                            <div class="spec-title">Makan Malam</div>
                                            @if (count($malam))
                                                <ul class="spec-list">
                                                    @foreach ($malam as $item)
                                                        <li>{{ $item }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>


                                <td>{{ $m->created_at }}</td>
                                <td>{{ $m->updated_at }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="">
                                                <a class="dropdown-item" href="{{ route('admin.menuMakanan.edit', $m->id) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>

                                            <form 
                                            action="{{ route('admin.menuMakanan.delete', $m->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus menu ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="q" value="{{ request('q') }}">
                                                <input type="hidden" name="per_page"
                                                    value="{{ request('per_page', $perPage ?? 10) }}">
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
                                <td colspan="8" class="text-center text-muted">
                                    Tidak ada data{{ request('q') ? ' untuk pencarian ini' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div class="small text-muted">
                    @if ($menus->total() > 0)
                        Menampilkan {{ $menus->firstItem() }}–{{ $menus->lastItem() }} dari {{ $menus->total() }} data
                    @else
                        Menampilkan 0–0 dari 0 data
                    @endif
                </div>
                {{ $menus->appends(request()->query())->links('pagination::bootstrap-5-ellipses') }}
            </div>
        </div>
    </div>
@endsection
