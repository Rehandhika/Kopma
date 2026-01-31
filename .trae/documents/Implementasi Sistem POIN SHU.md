## Jawaban Pertanyaan Anda (Redeem/Pencairan)
- Dalam desain yang akan diimplementasikan, **ya: sistem redeem/pencairan akan ada** dan **riwayat pencairan tercatat** sebagai transaksi bertipe `redeem` (poin keluar) di ledger `shu_point_transactions`, serta tampil di detail mahasiswa dan bisa diekspor.

## Ringkasan Analisis Proyek
- Stack: Laravel 12 + Livewire v3 + Tailwind/Vite; pola utama CRUD 6 dashboard memakai Livewire components dan Blade layouts.
- POS/kasir: transaksi disimpan via [Pos::processPayment](file:///c:/laragon/www/DEPLOY%20SIKOPMA/app/Livewire/Cashier/Pos.php#L520-L651) yang membuat `sales` + `sale_items` dalam DB transaction.
- RBAC: Spatie Permission + menu gating via [menu.php](file:///c:/laragon/www/DEPLOY%20SIKOPMA/config/menu.php) dan middleware role/permission.
- Sudah ada sistem “poin” lain (penalti absensi), jadi Poin SHU harus dipisahkan namespace/tabel.
- Export Excel tersedia (maatwebsite/excel). PDF generator belum ada sebagai dependency utama.

## Desain Data 6 Domain (Poin SHU)
- Entitas **Mahasiswa** terpisah dari `users`:
  - Tabel `students`: `id`, `nim` (string 9 digit, unique), `full_name`, `points_balance` (bigint default 0), timestamps.
- Ledger transaksi untuk catat poin masuk/keluar termasuk pencairan:
  - Tabel `shu_point_transactions`:
    - `id`, `student_id`, `sale_id` (nullable),
    - `type` enum: `earn` | `redeem` | `adjust`,
    - `amount` (nullable, nominal pembelian jika earn),
    - `percentage_bps` (int, snapshot persentase saat earn),
    - `points` (signed bigint: earn positif, redeem negatif),
    - `cash_amount` (nullable, nominal pencairan jika redeem),
    - `notes` (nullable), `created_by` (user_id), `created_at`.
  - Index: `(student_id, created_at)`, `(type, created_at)` untuk filter laporan.
  - Unique `(sale_id, type)` untuk mencegah duplikasi `earn` per sale.
- Keterkaitan ke transaksi kasir:
  - Tambah kolom ke `sales`: `student_id` nullable + `shu_points_earned` bigint default 0 + `shu_percentage_bps` int default 0.

## Algoritma Perhitungan Poin
- Persentase disimpan sebagai **basis points (bps)** untuk akurasi:
  - 2.50%  `250`.
  - Rumus: `points = floor(total_amount * percentage_bps / 10000)`.
- Service `ShuPointService`:
  - `getPercentageBps()` dari `settings` (cache pendek).
  - `computeEarnedPoints(int $amount, int $percentageBps): int` (unit-test).
  - `awardPointsForSale(...)`, `redeemPoints(...)`, `adjustPoints(...)`.

## Integrasi POS/Kasir
- Tambah field **NIM (opsional)** di modal pembayaran POS ([pos.blade.php](file:///c:/laragon/www/DEPLOY%20SIKOPMA/resources/views/livewire/cashier/pos.blade.php)).
  - Jika diisi: validasi `digits:9` 6 harus ada di `students`.
- Modifikasi `Pos` Livewire:
  - property `studentNim`.
  - di `processPayment()`: jika NIM valid, hitung poin, simpan snapshot di `sales`, insert ledger `earn`, update saldo.
  - tampilkan notifikasi poin yang didapatkan (toast + payload pada event `payment-success`).

## Dashboard Admin Poin SHU
- Tambah menu 6 routes `admin/poin-shu/*` dengan permission baru.
- Livewire pages:
  1. **Manajemen Mahasiswa (CRUD)**: list + search/filter + modal create/edit; enforce unique NIM.
  2. **Pengaturan Persentase Poin**: input 0–100% (0–10000 bps), simpan ke `settings` + audit trail.
  3. **Redeem/Pencairan Poin**:
     - Form pencairan: pilih mahasiswa (search NIM/nama), input `points_to_redeem` (harus <= saldo), opsional `cash_amount`, `notes`.
     - Simpan sebagai `shu_point_transactions.type=redeem` dengan `points` negatif + update saldo.
     - Halaman daftar pencairan (filter tanggal/NIM) + export.
  4. **Export**:
     - Excel: `StudentsExport`, `StudentTransactionsExport`, `RedemptionsExport` (chunking).
     - PDF: tambah paket PDF (opsi: `barryvdh/laravel-dompdf`) dan view khusus PDF.

## Halaman Monitoring Mahasiswa
- List mahasiswa + saldo poin:
  - Sorting: NIM/nama/poin; search cepat.
- Detail mahasiswa:
  - Riwayat transaksi poin gabungan: earn/redeem/adjust (poin masuk/keluar) + filter.
  - Badge/kolom khusus untuk menandai transaksi pencairan (redeem) + tampilkan `cash_amount` bila ada.

## Keamanan 6 Validasi
- RBAC (Spatie Permission):
  - permission contoh: `view.shu`, `manage.shu_students`, `manage.shu_settings`, `redeem.shu`, `export.shu`.
  - proteksi route via middleware permission/role; menu mengikuti `config/menu.php`.
- Audit trail konfigurasi:
  - saat persentase berubah: tulis ke `audit_logs` menggunakan [AuditLog](file:///c:/laragon/www/DEPLOY%20SIKOPMA/app/Models/AuditLog.php) (old/new) + ActivityLog.
- Validasi NIM: `digits:9`, unique di `students`.

## Konsistensi Saat Hapus Transaksi
- Sinkron dengan fitur hapus sale (POS Entry / Sales Report):
  - jika sale punya `shu_points_earned`/ledger `earn`, lakukan reversal (atau delete + decrement saldo) agar saldo konsisten.

## Testing 6 QA
- Unit: `computeEarnedPoints` (0%, 100%, bps desimal, rounding floor).
- Feature/integration:
  - POS dengan NIM menghasilkan poin + ledger + saldo.
  - POS tanpa NIM tidak mengubah poin.
  - Redeem mencatat transaksi `redeem` 6 menurunkan saldo, tidak boleh melewati saldo.
  - Hapus sale membalikkan poin.
- Performa:
  - paginasi + index untuk list mahasiswa 6 transaksi; export chunk.
  - seed/factory untuk simulasi 1000+ mahasiswa pada test.
- Keamanan:
  - test input NIM dengan payload injeksi ditolak validasi.

## Deliverables (yang akan diimplementasikan setelah Anda konfirmasi)
- Migrasi + model + relasi + service Poin SHU.
- POS: input NIM + awarding poin.
- Admin UI: CRUD mahasiswa, pengaturan %, monitoring, **redeem/pencairan**, export Excel/PDF.
- RBAC + audit trail.
- Test suite tambahan untuk poin.
