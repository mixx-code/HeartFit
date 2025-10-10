@extends('layouts.app')

@section('title', 'Edit Meal Package')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Meal Package</h5>
                    <small class="text-muted">Perbarui data di bawah ini</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.mealPackage.update', $mealPackage->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Nama Meal Package --}}
                        <div class="mb-3">
                            <label class="form-label" for="nama_meal_package">Nama Meal Package</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-restaurant"></i></span>
                                <input type="text" id="nama_meal_package" name="nama_meal_package"
                                    class="form-control @error('nama_meal_package') is-invalid @enderror"
                                    value="{{ old('nama_meal_package', $mealPackage->nama_meal_package) }}"
                                    placeholder="Contoh: Diet Booster" />
                                @error('nama_meal_package')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Batch --}}
                        <div class="mb-3">
                            <label class="form-label" for="batch">Batch</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-package"></i></span>
                                <input type="text" id="batch" name="batch"
                                    class="form-control @error('batch') is-invalid @enderror"
                                    value="{{ old('batch', $mealPackage->batch) }}" placeholder="Batch (opsional)" />
                                @error('batch')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Package Type --}}
                        <div class="mb-3">
                            <label class="form-label" for="package_type_id">Package Type</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-category"></i></span>
                                <select id="package_type_id" name="package_type_id"
                                    class="form-select @error('package_type_id') is-invalid @enderror">
                                    @foreach ($packageTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('package_type_id', $mealPackage->package_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->packageType }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('package_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Jenis Paket --}}
                        @php
                            $options = [
                                'paket 3 bulanan' => 'Paket 3 Bulanan',
                                'paket bulanan'   => 'Paket Bulanan',
                                'paket mingguan'  => 'Paket Mingguan',
                                'harian'          => 'Harian',
                            ];
                        @endphp
                        <div class="mb-3">
                            <label class="form-label" for="jenis_paket">Jenis Paket</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <select id="jenis_paket" name="jenis_paket"
                                class="form-select @error('jenis_paket') is-invalid @enderror">
                                @foreach ($options as $value => $label)
                                    <option value="{{ $value }}"
                                    {{ old('jenis_paket', $mealPackage->jenis_paket ?? null) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                    </option>
                                @endforeach
                                </select>
                                @error('jenis_paket')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Total Hari --}}
                        <div class="mb-3">
                            <label class="form-label" for="total_hari">Total Hari</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-package"></i></span>
                                <input type="number" id="total_hari" name="total_hari"
                                    class="form-control @error('total_hari') is-invalid @enderror"
                                    value="{{ old('total_hari', $mealPackage->total_hari) }}" placeholder="total_hari (opsional)" />
                                @error('total_hari')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Porsi Paket (dropdown opsi tetap) --}}
                        <div class="mb-3">
                            <label class="form-label" for="porsi_paket">Porsi Paket</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-bowling-ball"></i></span>
                                <select id="porsi_paket" name="porsi_paket"
                                    class="form-select @error('porsi_paket') is-invalid @enderror">
                                    <option value="">-- Pilih Porsi Paket --</option>
                                    @foreach ($porsiOptions as $opt)
                                        <option value="{{ $opt }}"
                                            {{ old('porsi_paket', $mealPackage->porsi_paket) === $opt ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('porsi_paket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Detail Paket --}}
                        <div class="mb-3">
                            <label class="form-label" for="detail_paket">Detail Paket</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-detail"></i></span>
                                <textarea id="detail_paket" name="detail_paket" rows="3"
                                    class="form-control @error('detail_paket') is-invalid @enderror" placeholder="Tuliskan detail paket">{{ old('detail_paket', $mealPackage->detail_paket) }}</textarea>
                                @error('detail_paket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Harga --}}
                        <div class="mb-3">
                            <label class="form-label" for="price">Harga</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-money"></i></span>
                                <input type="text" id="price_display" class="form-control"
                                    placeholder="Contoh: Rp150.000"
                                    value="{{ old('price', $mealPackage->price ? 'Rp' . number_format($mealPackage->price, 0, ',', '.') : '') }}" />
                                <input type="hidden" id="price" name="price"
                                    value="{{ old('price', $mealPackage->price) }}">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.mealPackage') }}" class="btn btn-outline-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const priceInput = document.getElementById('price');
        const priceDisplay = document.getElementById('price_display');

        priceDisplay.addEventListener('input', function() {
            let rawValue = this.value.replace(/[^\d]/g, '');
            priceInput.value = rawValue;

            if (rawValue) {
                this.value = formatRupiah(rawValue);
            } else {
                this.value = '';
            }
        });

        function formatRupiah(angka) {
            return 'Rp' + angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    </script>
@endpush
