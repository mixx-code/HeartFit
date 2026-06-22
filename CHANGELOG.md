# Changelog

## [Unreleased] - 2026-06-23 (rev 2)

### Added
- **Riwayat Pengantaran** — section riwayat pengantaran ditambahkan di dashboard customer, admin, dan superadmin; menampilkan histori pengiriman sebelum tanggal hari ini
- **Riwayat Pengantaran pagination** — pagination JS client-side (5 baris/halaman) di semua dashboard agar tidak terlalu panjang ke bawah
- **Menu "Data Saya"** — admin, superadmin, dan ahli_gizi dapat melihat dan mengupdate profil sendiri via menu sidebar "Data Saya" (`/staff/profil`)
- **Kolom "Nama Penerima"** — ditambahkan di list order admin, ahli_gizi, superadmin, dan customer
- **Kolom "No. Order"** — ditambahkan di list order admin
- **KPI dashboard admin** — card "Pesanan Hari Ini", "Terkirim", "Sedang Dikirim", "Gagal" kini terisi dari kalkulasi real (sebelumnya selalu 0 karena `$kpi` tidak pernah dikirim dari controller)
- **Seeder customer2–5** — tambah 4 akun customer dengan order aktif berbeda-beda: Slim & Fresh 24hr, Vegan Delight 4hr 2x, Protein Pack 12hr 2x, Diet Booster 36hr 2x

### Added
- **Generate Delivery per order** — command `heartfit:generate-delivery-statuses` diubah total: sekarang membaca order PAID yang `service_dates`-nya mengandung tanggal target, lalu generate 1 delivery record per order dengan `meal_package_id` yang benar; hasilnya admin melihat 4 record terpisah (Slim & Fresh, Vegan Delight, Protein Pack, Diet Booster) bukan hanya 1 "Vegan Delight"

### Changed
- **Customer dashboard delivery isolation** — query `$items` dan `$history` kini juga filter `meal_package_id = activeOrder->meal_package_id` agar setiap customer hanya melihat delivery milik paket mereka sendiri
- **Order customer1** — `meal_package_id` diubah dari `null` ke `4` (Slim & Fresh) di seeder agar filter delivery isolasi berjalan benar
- **Foto KTP** — upload foto KTP dihapus dari form profil staff (admin/superadmin/ahli_gizi); hanya customer yang bisa upload KTP
- **Kolom "Customer"** — diganti label menjadi "Nama Penerima" di list order ahli_gizi dan superadmin
- **Alamat di list order** — dihapus dari tampilan kolom Nama Penerima di semua list order (hanya nama saja)
- **Password admin** — diubah menjadi `admin123` di seeder
- **Generate Delivery command** — filter `serve_days` diubah dari hari-dalam-seminggu (1–7) ke tanggal-dalam-bulan (1–31) agar sesuai dengan data menu yang ada
- **Seeder delivery** — riwayat pengantaran hanya berisi 24 record milik customer1 (2026-05-24 s/d 2026-06-16); record tidak terkait customer dihapus
- **customer4** — metode pembayaran diubah dari COD ke transfer

### Fixed
- Bug: customer dengan order selesai ikut melihat status pengantaran hari ini milik customer lain — `$items` di `DashboardCustomerController` kini difilter dengan `service_dates` order
- Bug: riwayat pengantaran customer bisa bercampur dengan customer lain yang kebetulan punya batch dan menu yang sama — isolasi via `whereIn('delivery_date', $serviceDates)`
- Bug: semua KPI di dashboard admin selalu tampil 0 — `$kpi` tidak pernah dihitung dan dikirim dari controller

---

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
- Bug: tombol Edit di list customer tidak berfungsi (href="#")
- Security: halaman profil customer tidak memvalidasi kepemilikan — customer bisa akses profil orang lain dengan mengubah ID di URL; route profil diubah tanpa parameter ID, controller selalu load dari `Auth::user()->detail`
