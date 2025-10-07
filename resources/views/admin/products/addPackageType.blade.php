@extends('layouts.app')

@section('title', 'Add Package Type')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add Package Type</h5>
                    <small class="text-muted float-end">Lengkapi data berikut</small>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.products.packageType.addPackageType') }}">
                        {{-- Nama Menu --}}
                        <div class="mb-3">
                            <label class="form-label" for="package-type">Package Type</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-star"></i></span>

                                <input type="text" id="package-type" name="package-type" class="form-control"
                                    placeholder="Contoh: Premium" required>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
