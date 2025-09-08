@extends('layouts.app')

@section('title', 'Petugas/Admin')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                {{-- Judul di kiri --}}
                <h5 class="mb-0">Table Petugas/Admin</h5>

                {{-- Form + tombol di kanan --}}
                <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                    {{-- Search + Page size (GET) --}}
                    <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('petugas.index') }}">
                        <div class="input-group" style="min-width: 280px;">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Cari nama, NIK, alamat, dll...">
                        </div>
                        <div class="input-group" style="max-width: 160px;">
                            <span class="input-group-text">Rows</span>
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach ([5, 10, 15, 20] as $size)
                                    <option value="{{ $size }}"
                                        {{ (int) request('per_page', $perPage) === $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Search</button>
                        @if (request('q'))
                            <a href="{{ route('petugas.index', ['per_page' => request('per_page', $perPage)]) }}"
                                class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </form>

                    {{-- Tombol Tambah Data --}}
                    <a class="btn btn-success" href="{{ route('petugas.create') }}">
                        <i class="bi bi-plus-circle"></i> Tambah Data
                    </a>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Alamat</th>
                            <th>jenis kelamin</th>
                            <th>Tempat Tanggal Lahir</th>
                            <th>Posisi/Jabatan</th>
                            <th>Email</th>
                            <th>Nomor HP</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($petugas as $c)
                            <tr>
                                <td>{{ $c['nama'] }}</td>
                                <td>{{ $c['nik'] }}</td>
                                <td>{{ $c['alamat'] }}</td>
                                <td>{{ $c['jenis_kelamin'] }}</td>
                                <td>{{ $c['ttl'] }}</td>
                                <td>{{ $c['posisi_jabatan'] }}</td>
                                <td>{{ $c['email'] }}</td>
                                <td>{{ $c['hp'] }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"><i class="bx bx-edit-alt me-1"></i>
                                                Edit</a>
                                            {{-- DELETE --}}
                                            <form action="{{ route('petugas.destroy', $c['id']) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus {{ $c['nama'] }}?');">
                                                @csrf
                                                @method('DELETE')
                                                {{-- pertahankan query agar kembali ke kondisi saat ini --}}
                                                <input type="hidden" name="q" value="{{ request('q') }}">
                                                <input type="hidden" name="per_page"
                                                    value="{{ request('per_page', $perPage) }}">
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
                                <td colspan="9" class="text-center text-muted">Tidak ada
                                    data{{ request('q') ? ' untuk pencarian ini' : '' }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div class="small text-muted">
                    @if ($petugas->total() > 0)
                        Menampilkan {{ $petugas->firstItem() }}–{{ $petugas->lastItem() }} dari
                        {{ $petugas->total() }} data
                    @else
                        Menampilkan 0–0 dari 0 data
                    @endif
                </div>
                {{-- Pagination + pertahankan query (q, per_page) --}}
                {{ $petugas->appends(request()->query())->links('pagination::bootstrap-5-ellipses') }}
            </div>
        </div>
    </div>
@endsection
