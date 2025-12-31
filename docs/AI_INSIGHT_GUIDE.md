# AI Insight - Analisis Otomatis Penjualan POS

## Deskripsi Fitur

Fitur **AI Insight** secara otomatis menganalisis data penjualan setelah proses tutup toko (end of day) dan menampilkan insight yang mudah dipahami untuk membantu pengambilan keputusan bisnis.

## Cara Kerja

### 1. Automatic Generation
- Insight akan **otomatis dibuat** setiap kali Anda menutup cash register
- Sistem menganalisis seluruh transaksi dalam periode sesi POS yang baru saja ditutup
- Tidak perlu menunggu 1 hari - insight langsung tersedia

### 2. Analisis yang Dilakukan

Setiap insight mencakup:

#### 📊 Ringkasan Penjualan
- Total penjualan hari ini
- Jumlah transaksi
- Rata-rata nilai transaksi
- Total diskon yang diberikan

#### 🏆 Produk Terlaris
- Top 3 produk dengan penjualan tertinggi
- Jumlah unit terjual
- Revenue per produk

#### 📉 Produk Perlu Perhatian
- Produk dengan penjualan rendah
- Rekomendasi untuk promo atau bundling

#### ⏰ Pola Jam Operasional
- Jam ramai (peak hour)
- Jam sepi (quiet hour)
- Jumlah transaksi per jam

#### 📈/📉 Perbandingan dengan Hari Sebelumnya
- Selisih penjualan (Rp dan %)
- Selisih jumlah transaksi
- Trend naik/turun

#### 💡 Rekomendasi Aksi
Sistem memberikan rekomendasi berdasarkan data, seperti:
- **Stok**: Pastikan stok produk terlaris cukup
- **Promo**: Buat promo untuk produk slow-moving
- **Strategi**: Optimalkan jam ramai dengan kesiapan staff
- **Alert**: Peringatan jika penjualan turun signifikan

### 3. Tingkat Prioritas (Severity)

- **Info** (🔵): Informasi umum
- **Warning** (🟡): Perlu perhatian
- **Critical** (🔴): Segera ditindaklanjuti

## Cara Menggunakan

### Melihat Insight Setelah Tutup Toko

1. Buka halaman **Point of Sale**
2. Klik **"Tutup Toko"**
3. Isi jumlah uang kas fisik
4. Centang "Ini penutupan terakhir hari ini" jika sudah selesai untuk hari itu
5. Klik **"Tutup Sesi"**
6. Modal **AI Insight** akan muncul otomatis dengan analisis lengkap
7. Baca insight dan rekomendasi
8. Klik **"Ke Dashboard"** untuk melanjutkan

### Melihat Riwayat Insight

1. Buka menu **AI Insights** di sidebar
2. Filter berdasarkan:
   - Status (Unread/Active)
   - Severity (Info/Warning/Critical)
3. Klik insight untuk melihat detail lengkap

### Mengelola Insight

- **Mark as Read**: Tandai insight sudah dibaca
- **Dismiss**: Sembunyikan insight yang tidak relevan lagi
- **Mark All as Read**: Tandai semua insight sudah dibaca sekaligus

## Technical Details

### Service Layer
File: `app/Services/AiInsightService.php`

Metode utama:
- `generateDailyInsight(CashRegister $register)` - Generate insight setelah tutup toko
- `analyzeSalesData()` - Analisis data penjualan
- `analyzeHourlyPattern()` - Analisis pola per jam
- `compareWithPreviousDay()` - Bandingkan dengan hari sebelumnya
- `generateRecommendations()` - Buat rekomendasi aksi

### Controller
File: `app/Http/Controllers/AiInsightController.php`

Routes:
- `GET /ai-insights` - List semua insights
- `GET /ai-insights/{id}` - Detail insight
- `POST /ai-insights/{id}/read` - Mark as read
- `POST /ai-insights/{id}/dismiss` - Dismiss insight
- `GET /ai-insights/calendar/summary` - Calendar view
- `GET /ai-insights/calendar/daily` - Daily insights

### Integration Point
File: `app/Http/Controllers/CashRegisterController.php`

Pada method `processClose()`, setelah cash register ditutup:
```php
$aiService = new AiInsightService();
$insight = $aiService->generateDailyInsight($register);
```

Response JSON mencakup data insight yang kemudian ditampilkan di modal.

### Database
Table: `ai_insights`

Columns:
- `outlet_id` - ID outlet
- `type` - Jenis insight (sales_trend, stock_prediction, dll)
- `title` - Judul insight
- `content` - Konten insight (markdown format)
- `data` - Data lengkap (JSON)
- `severity` - Tingkat prioritas (info/warning/critical)
- `is_read` - Status sudah dibaca
- `is_dismissed` - Status di-dismiss
- `insight_date` - Tanggal insight

## Customization

### Mengubah Kriteria Rekomendasi

Edit file `app/Services/AiInsightService.php` pada method `generateRecommendations()`:

```php
// Contoh: Ubah threshold penjualan turun dari -10% menjadi -15%
if ($previousDayComparison['sales_percentage'] < -15) {
    // ... rekomendasi
}
```

### Menambah Jenis Analisis Baru

1. Tambahkan method analisis di `AiInsightService`
2. Panggil method di `analyzeSalesData()`
3. Update `generateInsightContent()` untuk format output
4. Update view jika perlu visual tambahan

### Mengubah Format Display

Edit file `resources/views/main/cash-register/close.blade.php`:

Function JavaScript `formatInsightContent()` untuk mengubah cara render markdown ke HTML.

## Tips Penggunaan

1. **Konsistensi**: Tutup toko pada waktu yang sama setiap hari untuk perbandingan yang akurat
2. **Review Rutin**: Cek insight setiap hari untuk trend penjualan
3. **Action Items**: Implementasikan rekomendasi yang diberikan
4. **Track Progress**: Bandingkan insight dari minggu ke minggu
5. **Staff Training**: Pastikan semua staff memahami cara membaca insight

## Troubleshooting

### Insight Tidak Muncul
- Pastikan ada transaksi dalam sesi
- Cek log di `storage/logs/laravel.log`
- Periksa koneksi database

### Data Tidak Akurat
- Pastikan semua transaksi tercatat dengan benar
- Cek setting timezone aplikasi
- Verifikasi data produk dan kategori

### Performance Issue
- Jika transaksi sangat banyak, pertimbangkan background job
- Implementasi caching untuk data yang sering diakses

## Roadmap

Fitur yang akan datang:
- [ ] Machine learning untuk prediksi stok
- [ ] Analisis pelanggan (customer behavior)
- [ ] Notifikasi push untuk insight critical
- [ ] Export insight ke PDF
- [ ] Perbandingan multi-outlet
- [ ] Insight berbasis AI yang lebih advanced

## Support

Jika ada pertanyaan atau masalah, hubungi tim development atau buat issue di repository.

---

**Version**: 1.0.0  
**Last Updated**: 2025-12-31  
**Author**: CuanFlow Development Team
