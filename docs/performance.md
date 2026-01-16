# Performa Halaman Publik (Katalog / Produk / Tentang)

Target: terasa instan untuk user, dan secepat mungkin menuju \u003c1s pada warm cache.

## Budget (Awal)
- Initial JS per halaman publik: \u2264 150KB gzip (ideal), \u2264 200KB gzip (maks).
- Request setelah first paint: 0\u20131 untuk state awal (ideal 0 lewat initial data inject).
- LCP: \u2264 1.0\u20131.5s di jaringan cepat.

## Cara Ukur (Manual)
1. Chrome DevTools \u2192 Network \u2192 Disable cache (untuk cold) / enable cache (untuk warm).
2. DevTools \u2192 Performance \u2192 rekam load halaman `/`, `/about`, `/products/{slug}`.
3. Catat: TTFB, FCP, LCP, jumlah request, dan total transferred.

## Baseline Build (Saat Dokumen Dibuat)
Hasil `npm run build`:
- main: 394.33 kB (gzip 123.45 kB)
- css: 157.69 kB (gzip 23.85 kB)

## Setelah Optimasi (Code Splitting + Data Inject + Caching)
Ringkas:
- Entry publik `main` turun menjadi ~196\u2013197 kB (gzip ~62 kB)
- Page dipisah menjadi chunk: `HomePage`, `AboutPage`, `ProductDetailPage`, `BannerCarousel`, dll.

Dampak yang diharapkan:
- Render awal lebih cepat (lebih sedikit JS yang harus diparse/execute)
- Navigasi reload lebih cepat berkat ETag + cache browser untuk API public
- Katalog/Tentang/Detail Produk bisa render dari initial data tanpa nunggu fetch awal

