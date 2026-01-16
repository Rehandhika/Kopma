## Tujuan

* Menambahkan section **Struktur Kepengurusan + Anggota** di halaman **Tentang Kami** dengan desain “epik” mengikuti referensi (tabs pill gradient, avatar ring, grid kartu) dan performa tinggi.

* Struktur dibagi menjadi 5 divisi: **BPH, IT, Toko, Humsar, Desain**, plus tab **Semua**.

## Kondisi Saat Ini (Ringkas)

* Halaman tentang ada di [AboutPage.jsx](file:///c:/laragon/www/Kopma/resources/js/react/pages/AboutPage.jsx) dengan layout “glass card” shadcn-style.

* Komponen shadcn React yang tersedia belum punya `Tabs` dan `Avatar`; hanya ada menubar/dropdown/popover/sheet, dll.

* Data “about” sudah di-seed via `initialData.about` (tanpa request ekstra) sehingga kita bisa menambah section kepengurusan tanpa membebani performa.

## Prinsip Performa (Wajib)

* Tanpa library animasi berat (tidak pakai framer-motion). Animasi cukup via `tailwindcss-animate` + CSS transition.

* Data kepengurusan default **static build-time** (JS/TS file) agar:

  * Render instan, tanpa fetch tambahan.

  * Minim request jaringan.

* Foto anggota opsional dan tetap ringan:

  * Gunakan thumbnail kecil + `loading="lazy"` untuk avatar non-hero.

  * Fallback inisial/icon jika foto kosong.

## Paket/Komponen yang Akan Ditambahkan

### Paket (minimal, performa tinggi)

* Tambah Radix primitive yang ringan:

  * `@radix-ui/react-tabs` untuk filter/tab (keyboard accessible).

  * `@radix-ui/react-avatar` untuk avatar + fallback.

### Komponen shadcn (baru) di `resources/js/components/ui`

* `tabs.jsx`: implementasi shadcn-style Tabs yang mendukung styling “pill/gradient” via className.

* `avatar.jsx`: implementasi shadcn-style Avatar (image + fallback inisial).

## Desain UI Mengikuti Referensi

###

### 2) Tabs “Semua / BPH / IT / Toko / Humsar / Desain”

* Bentuk: kapsul panjang dengan gradient (pink → ungu) seperti referensi.

* Responsif:

  * Mobile: `overflow-x-auto` + scroll halus.

  * Desktop: center.

* Interaksi smooth:

  * Active tab: highlight pill, transisi 200–300ms.

### 3) Layout Struktur

* Untuk tab **Semua**:

  * Render semua divisi berurutan.

  * Setiap divisi punya heading besar + subheading.

  * “Kepala divisi”/koordinator ditampilkan lebih menonjol.

* Untuk tab per divisi:

  * Hanya render section divisi tersebut.

* Pola kartu anggota:

  * Avatar lingkaran dengan ring warna per divisi.

  * Badge role (mis. “Ketua”, “Koordinator”, “Anggota”).

  * Nama uppercase/semibold seperti referensi.

### 4) Warna Ring Per Divisi sama

## Data Model (Agar Mudah Update Anggota)

* Buat satu file data, misalnya:

  * `resources/js/react/data/organization.ts` (atau `.js` jika ingin tanpa typing)

* Struktur:

  * `period`, `divisions[]`.

  * `division`: `key`, `label`, `description`, `ringClass`, `leaders[]`, `members[]`.

  * `member`: `id`, `name`, `role`, `title` (opsional), `photoUrl` (opsional), `featured` (opsional).

* Tujuan: Anda tinggal tambah/edit array untuk update anggota (tanpa menyentuh UI).

## Implementasi di AboutPage

* Tambahkan section baru “Kepengurusan” setelah blok “Jam Operasional” di [AboutPage.jsx](file:///c:/laragon/www/Kopma/resources/js/react/pages/AboutPage.jsx).

* Pecah jadi komponen kecil untuk rapi dan aman:

  * `OrganizationSection`

  * `DivisionTabs`

  * `LeaderRow`

  * `MemberGrid`

* Pastikan tidak mengganggu existing content “About/Contact/Operating hours”.

## Aksesibilitas & UX

* Tabs fully keyboard navigable (Radix).

* Avatar punya alt/fallback.

* Kontras teks aman untuk dark/light.

* Semua link (jika nanti ada IG/WA anggota) memakai `rel="noreferrer"`.

## Verifikasi

* Build: `npm run build` harus lolos.

* Cek responsive:

  * Mobile: tabs bisa scroll, card grid rapih.

  * Desktop: StoreStatus tetap center navbar (tidak terkait section tapi pastikan tidak regresi layout).

* Performance sanity:

  * Tidak menambah request baru.

  * Bundle AboutPage tetap chunk terpisah.

## Output Akhir yang Anda Dapatkan

* Section kepengurusan dengan desain sesuai referensi (tabs gradient + avatar ring + grid anggota).

* Data anggota terstruktur rapi dalam 1 file yang mudah di-update.

* Komponen UI shadcn `Tabs` dan `Avatar` siap dipakai di tempat lain.

