@extends('layouts.app')

@section('title', 'Edit Package Type')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Package Type</h5>
        <small class="text-muted float-end">Perbarui data berikut</small>
      </div>

      <div class="card-body">
        <form method="POST" action="{{ route('admin.packageType.update', $packageType->id) }}">
          @csrf
          @method('PUT')

          {{-- Package Type --}}
          <div class="mb-3">
            <label class="form-label" for="packageType">Package Type</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="bx bx-star"></i></span>
              <input
                type="text"
                id="packageType"
                name="packageType"
                class="form-control @error('packageType') is-invalid @enderror"
                placeholder="Contoh: Premium"
                value="{{ old('packageType', $packageType->packageType) }}"
                required
              >
              @error('packageType')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="d-flex justify-content-between">
            <a href="{{ route('admin.packageType') }}" class="btn btn-outline-secondary">Kembali</a>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection
