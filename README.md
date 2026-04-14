# CuanFlow - Smart POS & Business Management System

[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

**CuanFlow** is a modern, feature-rich Point of Sale (POS) and business management system built with Laravel 12. It is designed to help small to medium-sized businesses manage their operations efficiently, from inventory and production to sales and financial reporting, enhanced with AI-driven insights.

---

## Key Features

### Point of Sale (POS)
- **Fast Checkout**: Intuitive interface for quick transactions.
- **Multiple Payment Methods**: Supports Cash, Transfer, and Integrated Online Payments (Midtrans).
- **Discount & Voucher System**: Flexible discount rules (Percentage, Fixed, Buy X Get Y) and voucher code validation.
- **Cash Register**: Track opening/closing balances and daily cash flow.

### Inventory & Production
- **Raw Material Management**: Track stock levels and supplier information.
- **HPP (COGS) Calculator**: Automatically calculate Cost of Goods Sold for products.
- **Recipe Management**: Define recipes for products and automatically deduct raw materials upon production.
- **AI Recipe Generator**: Leverage AI to suggest or optimize product recipes.

### Finance & Analytics
- **Income & Expense Tracking**: Comprehensive logging of all financial movements.
- **Sales Analytics**: Detailed charts for sales trends, top products, and payment methods.
- **Export Reports**: Generate professional Excel and PDF reports.
- **AI Insights**: Automated business analysis and recommendations.

### Clara AI Assistant
- **Interactive AI**: Chat with Clara to ask about your business performance, get advice, or navigate the system.

---

## 🚀 Tech Stack & Integrations

> 📖 **Dokumentasi Lengkap:** Untuk detail seluruh daftar pustaka (_library_) yang digunakan beserta penjelasannya, silakan cek file [**TECH.md**](TECH.md).

Berikut adalah daftar teknologi dan library utama yang menggerakkan sistem CuanFlow:

| Teknologi / Library | Kategori | Deskripsi Library | Contoh Penggunaan di CuanFlow |
| :--- | :---: | :--- | :--- |
| ![Laravel](https://img.shields.io/badge/Laravel_12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white) | Framework | Framework PHP terstruktur berbasis MVC backend. | Fondasi utama sistem backend, routing web/API, ORM Eloquent. |
| ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white) | Database | Sistem penyimpan data rasional (_RDBMS_). | Menyimpan semua data produk, transaksi kasir, dan akun user. |
| ![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white) | Frontend UI | _Utility-first_ framework CSS canggih. | Styling seluruh tampilan dashboard, komponen alert, dan form. |
| ![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white) | Interactivity | Minimal JS framework reaktif untuk interaktivitas file Blade. | Mengatur state dropdown menu kasir, _modal_, dan animasi navigasi. |
| ![Midtrans](https://img.shields.io/badge/Midtrans_PHP-001020?style=for-the-badge) | Payment | Solusi _Payment Gateway_ lengkap di Indonesia. | Integrasi halaman pelunasan hutang pelanggan (_debt payment_). |
| ![Gemini AI](https://img.shields.io/badge/Google_Gemini-8E75B2?style=for-the-badge&logo=google&logoColor=white) | Integrasi AI | Model kecerdasan buatan dari Google via API. | Fitur chatbot Clara AI dan generate ide produk/resep (otomasi). |
| ![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white) & ![ApexCharts](https://img.shields.io/badge/Apex-111?style=for-the-badge) | Analytics | Library JavaScript pengubah data menjadi grafik visual. | Merender metrik penjualan, laporan top item, & grafik CPU Monitor. |
| ![Spatie](https://img.shields.io/badge/Spatie_Packages-F2F2F2?style=for-the-badge&logo=php&logoColor=black) | Security & Utils | Paket esensial keamanan & otomasi dari Spatie. | Memanajemen control _Roles & Permission_ antar kasir/owner, Backup. |
| ![Leaflet](https://img.shields.io/badge/Leaflet-199900?style=for-the-badge&logo=leaflet&logoColor=white) | Maps | Library visualisasi Peta (Maps) ringan. | Digunakan pada fitur Sales Map untuk melacak letak titik transaksi. |

---

## Project Structure

A high-level overview of the project's folder structure:

```
CuanFlow/
├── app/
│   ├── Console/            # Artisan commands
│   ├── Events/             # Application events
│   ├── Exceptions/         # Exception handling
│   ├── Exports/            # Excel export classes
│   ├── Helpers/            # Helper functions
│   ├── Http/
│   │   ├── Controllers/    # Application controllers
│   │   ├── Middleware/     # Request filtering
│   │   ├── Requests/       # Form validation
│   │   └── Resources/      # API resources
│   ├── Jobs/               # Queued jobs
│   ├── Listeners/          # Event listeners
│   ├── Mail/               # Mailable classes
│   ├── Models/             # Eloquent models
│   ├── Notifications/      # Notification classes
│   ├── Observers/          # Model observers
│   ├── Policies/           # Authorization policies
│   ├── Providers/          # Service providers
│   ├── Services/           # Business logic services
│   └── View/Components/    # Blade components
├── config/                 # Application configuration
├── database/
│   ├── factories/          # Model factories
│   ├── migrations/         # Database schema migrations
│   └── seeders/            # Database seeders
├── public/                 # Web server root
├── resources/
│   ├── css/                # Tailwind CSS
│   ├── js/                 # JavaScript assets
│   └── views/              # Blade templates
├── routes/
│   ├── api.php             # API routes
│   ├── channels.php        # Broadcasting channels
│   ├── console.php         # Console commands
│   └── web.php             # Web routes
├── storage/                # Logs, compiled templates, file uploads
├── tests/                  # Automated tests
└── vendor/                 # Composer dependencies
```

---

## Getting Started

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL or other supported database

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/DapCodes/CuanFlow.git
    cd CuanFlow
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    npm install
    ```

3.  **Environment Setup:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database and API keys in `.env`.*

4.  **Run Migrations & Seeders:**
    ```bash
    php artisan migrate --seed
    ```

5.  **Build Assets:**
    ```bash
    npm run build
    ```

6.  **Start the Server:**
    ```bash
    php artisan serve
    ```

---

## Collaboration & Contribution

We welcome contributions to make CuanFlow even better!

1.  **Fork the Project**
2.  **Create your Feature Branch** (`git checkout -b feature/AmazingFeature`)
3.  **Commit your Changes** (`git commit -m 'Add some AmazingFeature'`)
4.  **Push to the Branch** (`git push origin feature/AmazingFeature`)
5.  **Open a Pull Request**

### Code Standards
-   Follow PSR-12 coding resulting using `php artisan pint`.
-   Ensure all tests pass using `php artisan test`.

---

## License

Distributed under the MIT License. See `LICENSE` for more information.

---

## Contact

**Daffa Ramadhan Maulana**  
Email: daffaramadhan929@gmail.com  
GitHub: [DapCodes](https://github.com/DapCodes)

Project Link: [https://github.com/DapCodes/CuanFlow](https://github.com/DapCodes/CuanFlow)
