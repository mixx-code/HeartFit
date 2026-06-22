# Changelog

## [Unreleased] - 2026-06-22

### Added
- Seeder akun role `admin` (`admin@mail.com` / `admin123`)

### Changed
- **Sidebar** — hapus section `medical_record` dan `bendahara` (role sudah tidak dipakai); bersihkan kondisi dashboard link dari kedua role tersebut
- **Sidebar** — hapus menu "List Orders" dari sidebar role `customer`
- **Sidebar** — hapus menu "Data Customers" dari sidebar role `ahli_gizi`
- **Login redirect** — `ahli_gizi` kini diarahkan ke dashboard ahli gizi (`ahli_gizi.orders`), bukan `dashboard.admin`
- **Route** — hapus `ahli_gizi`, `bendahara`, `medical_record` dari middleware route `dashboard.admin`
- **Dashboard customer** — status pengantaran kini hanya menampilkan data milik customer yang sedang login (filter berdasarkan `batch` dan `unique_menus` dari order PAID terbaru), bukan semua customer
- **Dashboard customer** — section "Pesan Sekarang" dan tombol "Pesan Sekarang" di modal detail paket disembunyikan jika customer sudah memiliki order berstatus PAID
- **Modal detail paket** — tambah null-check pada `modalOrderBtn` di JavaScript agar modal tidak crash saat tombol disembunyikan

### Fixed
- Bug: customer baru (belum order) melihat status pengantaran milik customer lain
- Bug: modal detail paket tidak terbuka karena JS error akibat elemen `modalOrderBtn` tidak ada di DOM
