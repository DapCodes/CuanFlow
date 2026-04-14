# Tech Stack Project CuanFlow

Dokumen ini berisi daftar lengkap teknologi, framework, library, dan tools yang digunakan dalam pengembangan project CuanFlow beserta penjelasannya.

## 1. Core Stack
- **Framework:** Laravel 12 - Framework PHP untuk membangun aplikasi web dengan struktur MVC.
- **Backend (BE):** Laravel
- **Frontend (FE):** Blade (Templating Engine bawaan Laravel) & Tailwind CSS (Utility-first CSS framework untuk styling).
- **Database:** MySQL - Relational Database Management System (RDBMS) yang digunakan untuk menyimpan seluruh data aplikasi.

## 2. Tools
- **Git:** Version Control System untuk melacak perubahan kode dan kolaborasi.
- **Docker:** Digunakan untuk setup environment development yang konsisten (saat ini belum digunakan di tahap production).

## 3. Backend Libraries (Composer)
Berikut adalah daftar library PHP yang ada pada `composer.json` untuk mendukung backend/sisi server:

### Bawaan Laravel / Umum Digunakan:
- **Eloquent ORM:** Object-Relational Mapper bawaan Laravel untuk mempermudah interaksi database.
- **Laravel Sanctum (`laravel/sanctum`):** Sistem autentikasi ringan untuk API SPA (Single Page Application) dan mobile auth.
- **Laravel Socialite (`laravel/socialite`):** Mengelola sistem login menggunakan akun platform ketiga (OAuth) contohnya Google Sign-In.
- **GuzzleHTTP (`guzzlehttp/guzzle`):** Client HTTP untuk mempermudah aplikasi melakukan request ke API eksternal.
- **Laravel Tinker (`laravel/tinker`):** Alat REPL (Read-Eval-Print Loop) bawaan yang membantu pengujian kode langsung lewat terminal berikatan dengan aplikasi.

### Pihak Ketiga (Non-Default Laravel):
- **Midtrans PHP (`midtrans/midtrans-php`):** Integrasi sistem payment gateway Midtrans untuk mengeksekusi dan memverifikasi pembayaran serta webhooks.
- **Spatie Laravel Permission (`spatie/laravel-permission`):** Memudahkan pengelolaan kontrol akses pengguna berbasis Roles & Permissions.
- **Yajra DataTables (`yajra/laravel-datatables-oracle`):** Menangani server-side rendering komponen tabel yang memudahkan pemrosesan data berjumlah besar tanpa membebani browser.
- **Maatwebsite Excel (`maatwebsite/excel`):** Memberikan fungsionalitas untuk melakukan aksi _import_ dan _export_ data dalam format `.xlsx` atau `.csv`.
- **Barryvdh Laravel DomPDF (`barryvdh/laravel-dompdf`):** Library untuk mengkonversi tampilan HTML/Blade menjadi bentuk dokumen PDF, berguna untuk membuat file laporan/struk.
- **Spatie Laravel Backup (`spatie/laravel-backup`):** Mengotomatisasi proses pembuatan cadangan/backup seluruh file dan database aplikasi.
- **Flysystem Google Drive Ext (`masbug/flysystem-google-drive-ext`):** Ekstensi filesystem (driver storage) untuk bisa mengunggah dan menyimpan file langsung ke cloud storage Google Drive, sering digabungkan dengan tool backup.
- **Spatie Laravel Activitylog (`spatie/laravel-activitylog`):** Merekam dan menyediakan log history atas setiap aktivitas (Create/Update/Delete) yang terjadi pada data model.
- **Simple QrCode & Bacon QrCode (`simplesoftwareio/simple-qrcode`, `bacon/bacon-qr-code`):** Berguna untuk diimplementasikan sebagai alat peng-generate kode QR dinamis.
- **Picqer PHP Barcode Generator (`picqer/php-barcode-generator`):** Memiliki fungsi untuk membuat kode batang (barcode), biasanya untuk label produk fisik.
- **Pusher PHP Server (`pusher/pusher-php-server`):** Memungkinkan backend berinteraksi dengan layanan websockets Pusher sebagai media broadcast dan notifikasi real-time ke klien.
- **Predis (`predis/predis`):** Sebuah sistem client Redis di PHP untuk mengatur _caching_, _sessions_, dan sistem antrian (_queues_).

## 4. Frontend Libraries & CDN Links
Kumpulan tautan _Content Delivery Network_ (library eksternal JavaScript dan CSS) berikut diimplementasikan pada `resources/views/layouts/app.blade.php`, `resources/views/layouts/app-sidebar.blade.php`, serta view form/modul terkait:

- **Tailwind CSS** (`https://cdn.tailwindcss.com`)
  - **Kegunaan:** Basis CSS utility-first framework untuk membuat komponen dan mendesain User Interface kustom pada seluruh halaman secara cepat langsung dari file Blade.
- **Alpine.js** (`https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/...`)
  - **Kegunaan:** Framework JavaScript ringan dan reaktif yang disematkan langsung di dalam HTML, berguna untuk fungsionalitas UI sederhana seperti interaksi toggle dropdown, state sidebar, dan modal pop-up.
- **SweetAlert2** (`https://cdn.jsdelivr.net/npm/sweetalert2@11`)
  - **Kegunaan:** Menggantikan alert browser standar menjadi kotak pop-up notifikasi konfirmasi interaktif dengan desain visual fleksibel dan menarik (sukses, loading, konfirmasi penghapusan data).
- **Satoshi Font** (`https://api.fontshare.com/v2/css?f[]=satoshi...`)
  - **Kegunaan:** Tipografi utama kustom dari Fontshare untuk menunjang nilai keluwesan desain modern pada aplikasi.
- **Phosphor Icons** (`https://unpkg.com/@phosphor-icons/web`) & **FontAwesome** (`https://cdnjs.cloudflare.com/ajax/libs/font-awesome/...`)
  - **Kegunaan:** Pasokan perpustakaan ikon format SVG untuk visual pendukung indikator pada menu navigasi, kartu, atau tombol.
- **Select2** (`https://cdn.jsdelivr.net/npm/select2`)
  - **Kegunaan:** Plugin interkatif ini mengubah tampilan tag element `<select>` tradisional menjadi combobox pintar dengan kotak input pencarian / auto-complete.
- **Choices.js** (`https://cdn.jsdelivr.net/npm/choices.js`)
  - **Kegunaan:** Solusi modern ringan pengganti Select2, biasanya digunakan pada page module/tugas tertentu untuk menyeleksi input berbasis tag dan kombo multi pilih teks.
- **Chart.js** & **ApexCharts** (`https://cdn.jsdelivr.net/npm/chart.js`, `https://cdn.jsdelivr.net/npm/apexcharts`)
  - **Kegunaan:** Bertanggung jawab dalam menerjemahkan barisan data analitik statistik dari backend (sales & target profit) dan dirangkai menjadi visual interaktif dan modern berupa grafik batang/garis pada dashboard (misal: CPU Monitoring & statistik keuangan).
- **Leaflet & MarkerCluster** (`https://unpkg.com/leaflet`, `https://unpkg.com/leaflet.markercluster/...`)
  - **Kegunaan:** Library perpetaan _open-source_, diintegrasikan khusus pada pemantauan geolokasi titik transaksi/pembelian dan titik lokasi outlet.
- **Swiper** (`https://cdn.jsdelivr.net/npm/swiper`)
  - **Kegunaan:** Sistem slide carousel ultra responsif dengan sentuhan interaktif sentuhan perangkat mobile, ditemukan pada slider area landing page dan form interaktif multistep (misalnya register).
- **SortableJS** (`https://cdn.jsdelivr.net/npm/sortablejs`)
  - **Kegunaan:** Library pemicu "Drag-and-Drop", digunakan pada penyeretan _list_ pengaturan form/section landing page agar komponennya bisa digeser letak urutannya.
- **QuillJS** (`https://cdn.quilljs.com/...`)
  - **Kegunaan:** Rich Text Editor/WYSIWYG modern yang diterapkan saat pengguna perlu memformat teks panjang (seperti konten dan deskripsi mendarat).
- **FullCalendar** (`https://cdn.jsdelivr.net/npm/fullcalendar`)
  - **Kegunaan:** Menghidangkan informasi list event operasional atau task layaknya Google Calendar, memberikan gambaran jadwal dan pengingat yang bisa dieksplor langsung di kalender dashboard.
