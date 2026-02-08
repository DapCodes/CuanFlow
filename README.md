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

## Tech Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: Tailwind CSS 4.0, Alpine.js, Blade Templates
- **Database**: MySQL
- **Integrations**: Midtrans (Payment), Gemini (AI), Maatwebsite Excel, Spatie Activitylog, Spatie Backup

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
