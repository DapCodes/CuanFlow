# Dokumentasi Sistem Peta Peluang Bisnis (CuanFlow Heatmap)

Sistem Peta Peluang Bisnis adalah fitur berbasis AI pada aplikasi CuanFlow yang membantu pemilik UMKM (seperti outlet Takoyaki Didi) mencari lokasi strategis terbaik untuk membuka cabang baru. Fitur ini menggunakan perbandingan data geografi (OpenStreetMap) dan algoritma AI untuk memberikan skor peluang di berbagai area.

---

## 🧭 Alur Kerja Sistem (Step-by-Step)

Sistem ini memiliki 3 tahap utama: Pengambilan Data (Fetching), Perhitungan & Klasifikasi AI (Calculation), dan Visualisasi (Map Display).

### TAHAP 1: Pengambilan Data Bisnis (OpenStreetMap)
Data mentah berupa titik koordinat bisnis (pesaing & fasilitas seperti sekolah, kantor) diambil melalui sistem OpenStreetMap (OSM) via Overpass API.

1. **Titik Eksekusi**:
   Admin menjalankan perintah di terminal server:
   `php artisan heatmap:fetch-osm --area=bandung` (atau lokasi lain seperti `cikarang`).
2. **File Eksekusi CLI**:
   - `app/Console/Commands/FetchOsmData.php`: Menangkap argumen `--area` atau `--bbox`, kemudian memanggil proses layanan.
3. **Layanan Pemrosesan**:
   - `app/Services/OsmDataService.php`: Memiliki daftar koordinat *Bounding Box* tiap kota. Memanggil API publik `overpass-api.de` untuk mengambil data `node` bertipe toko, kantor, sekolah, dll.
4. **Penyimpanan Data**:
   Data yang didapat disimpan ke dalam tabel di database menggunakan model `app/Models/BusinessPoint.php`. Model ini menyimpan Data mentah tag, Kategori, Sub Kategori, Latitude dan Longitude.

*Catatan: Jika dipanggil menggunakan flag `--queue`, perintah akan dijadwalkan secara background melalui `app/Jobs/FetchOsmDataJob.php`.*

---

### TAHAP 2: Perhitungan & Klasifikasi AI
Setelah titik-titik bisnis didapatkan, sistem harus membagi peta menjadi petak-petak (grid) dan memberikan skor/analisis AI pada setiap petaknya.

1. **Titik Eksekusi**:
   Admin menjalankan perhitungan skor (Haversine formula & AI):
   `php artisan heatmap:calculate --bounds="-7.10,-6.70,107.40,107.80"`
2. **File Eksekusi CLI**:
   - `app/Console/Commands/CalculateHeatmapScores.php`: Mengambil batas koordinat (bounds). Menghitung jarak antartitik.
3. **Perhitungan Skor Sistem**:
   Sistem membuat blok-blok grid di database melalui model `app/Models/GridArea.php`. Model ini merepresentasikan satu blok area di peta. Di dalam GridArea ini, algoritma yang kemungkinan ada di Service Layer akan memberikan skor mentah (Demand Score & Competition Score).
   Model `GridArea` memiliki scope query penting: `scopeWithinRadius` yang memakai **Rumus Haversine** SQL murni untuk mencari titik dalam radius KM tertentu secara efisien.
4. **Klasifikasi AI**:
   Setelah skor didapat, sistem mengirim data statistik grid tersebut ke API External / AI Model (OpenAI/Anthropic) untuk dibaca dan diberikan klasifikasi berupa label (`High Potential`, `Medium`, `Low`) serta analisis teks (contoh: "Area ini bagus karena banyak kantor tapi kurang pesaing restoran..."). Label ini disimpan kembali ke tabel `grid_areas`.

---

### TAHAP 3: Visualisasi & Antarmuka Pengguna
Tahap akhir adalah mengembalikan data tersebut agar bisa dilihat dengan interaktif oleh User (Pemilik Outlet).

1. **Validasi Akses Berlangganan (Subscription)**:
   - File: `app/Services/FeatureAccessService.php`.
   Fitur "Heatmap" ini adalah fitur premium. Sebelum pengguna membuka halaman, sistem (`FeatureAccessService`) akan mengecek tingkat langganan pengguna (Tier). Jika mereka masih dalam masa aktif, Grace Period, atau Administrator, akses diizinkan. Jika Expired, akses ditolak.
2. **Controller Tampilan Utama**:
   - File: `app/Http/Controllers/OpportunityMapController.php`.
   Controller ini sangat sederhana, hanya memiliki satu fungsi `index()` yang memuat kerangka HTML.
3. **Controller API AJAX (Backend Peta)**:
   - File: `app/Http/Controllers/Api/HeatmapController.php` (Disimpulkan ada dari rute AJAX di Frontend).
   Ketika halaman dimuat, Javascript akan memanggil `/api/v1/heatmap` beserta koordinat Outlet pengguna. Controller ini mengambil data `GridArea` menggunakan radius 15 KM (`withinRadius` model) dan memformatnya menjadi JSON.
4. **Tampilan Antarmuka (UI/Map)**:
   - File: `resources/views/opportunity/index.blade.php`.
   Ini adalah *otak frontend* dari fitur peta:
   - Menampilkan Peta Nusantara via Leaflet.js
   - Menarik koordinat Latitude & Longitude dari Outlet pengguna yang sedang login.
   - Mengirim AJAX Fetch Request ke API Heatmap secara reaktif (setiap digeser / filter diubah).
   - Menyediakan fitur *Auto-fit Bounds* (`map.fitBounds`) dengan radius ±15KM di sekitar titik outlet menggunakan offset derajat (`0.135`).
   - Menyediakan UI berbahasa Indonesia untuk memfilter warna/klasifikasi (Tinggi, Menengah, Rendah).

---

## 📌 Ringkasan File & Fungsinya

| File Path | Fungsi Utama |
| :--- | :--- |
| `app/Console/Commands/FetchOsmData.php` | CLI Command untuk men-trigger unduhan data OpenStreetMap. |
| `app/Jobs/FetchOsmDataJob.php` | Background job (Antrian) agar Fetching data tidak membuat server freeze / timeout. |
| `app/Services/OsmDataService.php` | Logika utama koneksi ke Overpass API, pendefinisian Bounding Box per kota, dan filter kategori. |
| `app/Models/BusinessPoint.php` | Model tabel penyimpanan titik-titik tunggal tiap toko/lokasi (Raw Data). |
| `app/Console/Commands/CalculateHeatmapScores.php` | CLI Command untuk membuat Grid, membagikan skor manual, dan memanggil AI Analyzer. |
| `app/Models/GridArea.php` | Model tabel dari blok-blok area analisis. Berisi rumus Radius *Haversine* SQL dan Skor AI. |
| `app/Services/FeatureAccessService.php` | *Gatekeeper*. Pengatur hak akses / Paywall bagi User CuanFlow berdasarkan status langganannya (Tiers). |
| `app/Http/Controllers/OpportunityMapController.php` | Controller yang menyajikan kerangka View HTML halaman Peta. |
| `app/Http/Controllers/Api/HeatmapController.php` | Penyedia API internal (AJAX) bagi Leaflet JS agar bisa mendapatkan data Grid dan Statistik dari database. |
| `resources/views/opportunity/index.blade.php` | UI Antarmuka pengguna (Leaflet JS, AJAX handler, Filter klasifikasi, Panel Statistik). |

---
*Dokumentasi ini di-generate otomatis untuk siklus pengembangan internal Tim CuanFlow.*
