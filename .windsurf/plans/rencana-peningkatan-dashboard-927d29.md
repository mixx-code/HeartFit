# Rencana Peningkatan Dashboard Customer

## Ringkasan
Rencana ini menangani dua persyaratan utama dari revisi.md:
1. **Tampilan Dashboard**: Ubah "Pesan Sekarang" menjadi "Detail Paket" untuk paket Reguler
2. **Alur Detail Paket**: Tampilkan detail paket saat diklik, lalu arahkan ke halaman pemesanan

## Analisis Kondisi Saat Ini

### Struktur Dashboard
- Dashboard customer menampilkan kartu paket untuk Reguler, Mingguan, Bulanan, 3 Bulanan
- Setiap paket memiliki tombol "Pesan Sekarang" yang langsung ke `orders.create`
- Perlu diubah menjadi tampil detail paket terlebih dahulu, baru ada opsi pemesanan

### Jenis Paket
- **Reguler**: Rp 50.000 - Rp 3.540.000 (berbagai durasi)
- **Premium**: Rp 170.000 - Rp 5.830.000 (berbagai durasi)
- **Personal**: Rp 700.000 (tipe paket baru)

## Rencana Implementasi

### Fase 1: Pembaruan Tampilan Dashboard

#### 1.1 Perbarui Layout Dashboard
- **File**: `resources/views/customers/dashboard.blade.php`
- **Perubahan**:
  - Ubah tombol "Pesan Sekarang" menjadi "Detail Paket" untuk paket Reguler
  - Tambahkan JavaScript untuk menampilkan detail paket
  - Buat modal/seksi untuk menampilkan detail saat diklik
  - Pertahankan fungsionalitas yang sudah ada untuk paket Premium dan Personal

#### 1.2 Tampilan Detail Paket
- **Konten yang ditampilkan**:
  - Nama dan harga paket
  - Opsi durasi (4 hari, 8 hari, 12 hari, 24 hari, 36 hari, 72 hari)
  - Detail menu (apa saja yang termasuk)
  - Manfaat/fitur
  - Tombol "Pesan Paket" untuk melanjutkan ke pemesanan

#### 1.3 Fungsionalitas JavaScript
- **Toggle detail**: Tampilkan/sembunyikan detail paket
- **Transisi halus**: UX profesional untuk membuka/menutup detail
- **Responsif mobile**: Bekerja di semua ukuran layar

### Fase 2: Modal/Detail Paket

#### 2.1 Buat Komponen Detail Paket
- **Desain**: Tampilan detail paket yang bersih dan informatif
- **Informasi**: Rincian harga, opsi durasi, contoh menu
- **Call-to-action**: Tombol "Pesan Paket" yang jelas
- **Styling**: Konsisten dengan sistem desain yang ada

#### 2.2 Titik Integrasi
- **Sumber data**: Gunakan data paket yang sudah ada di dashboard
- **Routing**: Terhubung ke route `orders.create` yang sudah ada
- **Manajemen state**: Tangani paket dan durasi yang dipilih

### Fase 3: Peningkatan Menu Popup

#### 3.1 Tampilan Menu di Pemilihan Tanggal
- **File**: `resources/views/customers/orders/create.blade.php`
- **Kondisi saat ini**: Langkah pemilihan tanggal dengan fungsionalitas menu
- **Peningkatan**: Tambahkan gambar makanan untuk membuat pilihan menu lebih menarik
- **Implementasi**: 
  - Tambah galeri gambar untuk item menu
  - Tampilkan foto makanan saat customer klik tombol "Menu"
  - Tingkatkan daya tarik visual antarmuka menu

#### 3.2 Integrasi Gambar
- **Sumber gambar**: Gunakan gambar makanan yang sudah ada atau tambah baru
- **Format tampilan**: Grid atau carousel layout
- **Performa**: Optimasi loading gambar dan caching

## Detail Teknis Implementasi

### Perubahan Dashboard
```php
// Ubah teks tombol dan tambah toggle detail
<a href="#" class="btn btn-outline-primary" onclick="togglePackageDetail('reguler')">Detail Paket</a>

// Tambahkan seksi detail paket
<div id="package-detail-reguler" class="package-detail" style="display:none;">
  <!-- Konten detail paket -->
</div>
```

### Fungsi JavaScript
```javascript
function togglePackageDetail(packageType) {
  // Toggle visibilitas detail paket
  // Tangani transisi halus
  // Update status tombol
}
```

### Peningkatan Menu
```php
// Tambahkan gambar ke tampilan menu
<div class="menu-popup">
  <div class="menu-images">
    <img src="path/to/makanan1.jpg" alt="Menu item 1">
    <img src="path/to/makanan2.jpg" alt="Menu item 2">
    <!-- Gambar lainnya -->
  </div>
</div>
```

## Persyaratan Testing

### Testing Fungsional
- Verifikasi tombol "Detail Paket" bekerja dengan benar
- Test tampilan/hilang detail paket
- Pastikan responsif di mobile
- Test alur pemesanan dari detail paket

### Testing Visual
- Periksa konsistensi desain di semua paket
- Verifikasi loading gambar di menu popup
- Test transisi dan animasi

### Testing Cross-browser
- Kompatibilitas Chrome, Firefox, Safari
- Testing browser mobile
- Layout tablet dan desktop

## Kriteria Sukses

### Dashboard
- [ ] Tombol "Pesan Sekarang" diubah menjadi "Detail Paket" untuk paket Reguler
- [ ] Detail paket tampil saat "Detail Paket" diklik
- [ ] Transisi halus dan styling profesional
- [ ] Desain responsif mobile

### Peningkatan Menu
- [ ] Gambar makanan ditambahkan ke popup pemilihan menu
- [ ] Daya tarik visual antarmuka menu ditingkatkan
- [ ] Loading gambar cepat dan optimasi

### Integrasi
- [ ] Alur mulus dari dashboard → detail paket → halaman pemesanan
- [ ] Pertahankan fungsionalitas yang sudah ada untuk paket lain
- [ ] Tidak ada perubahan yang merusak sistem pemesanan saat ini

## Estimasi Waktu
- **Fase 1** (Pembaruan dashboard): 2-3 jam
- **Fase 2** (Peningkatan menu): 1-2 jam
- **Testing & Perbaikan**: 1 jam
- **Total**: 4-6 jam

## Ketergantungan
- Struktur data paket yang sudah ada
- Sistem routing yang ada
- Framework CSS Bootstrap
- jQuery untuk interaksi JavaScript
