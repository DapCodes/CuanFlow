<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            // Sales Category
            ['name' => 'pos', 'display_name' => 'Point of Sale', 'category' => 'Sales', 'icon' => 'shopping-cart', 'route_name' => 'pos.index', 'sort_order' => 1],
            ['name' => 'sales_management', 'display_name' => 'Manajemen Penjualan', 'category' => 'Sales', 'icon' => 'receipt', 'route_name' => 'sales.index', 'sort_order' => 2],
            ['name' => 'discount_management', 'display_name' => 'Manajemen Diskon', 'category' => 'Sales', 'icon' => 'tag', 'route_name' => 'discounts.index', 'sort_order' => 3],
            ['name' => 'invoice_list', 'display_name' => 'Daftar Invoice', 'category' => 'Sales', 'icon' => 'file-text', 'route_name' => 'invoices.index', 'sort_order' => 4],

            // Finance Category
            ['name' => 'finance_management', 'display_name' => 'Manajemen Keuangan', 'category' => 'Finance', 'icon' => 'wallet', 'route_name' => 'finance.index', 'sort_order' => 10],
            ['name' => 'other_income', 'display_name' => 'Pendapatan Lain', 'category' => 'Finance', 'icon' => 'plus-circle', 'route_name' => 'finance.other-income', 'sort_order' => 11],
            ['name' => 'operational_costs', 'display_name' => 'Biaya Operasional', 'category' => 'Finance', 'icon' => 'minus-circle', 'route_name' => 'expenses.index', 'sort_order' => 12],
            ['name' => 'balance_withdrawal', 'display_name' => 'Penarikan Saldo', 'category' => 'Finance', 'icon' => 'credit-card', 'route_name' => 'withdraw.index', 'sort_order' => 13],
            ['name' => 'accounts_receivable', 'display_name' => 'Piutang Pelanggan', 'category' => 'Finance', 'icon' => 'users', 'route_name' => 'customer-debts.index', 'sort_order' => 14],

            // Inventory Category
            ['name' => 'products_recipes', 'display_name' => 'Produk & Resep', 'category' => 'Inventory', 'icon' => 'package', 'route_name' => 'products-hpp.index', 'sort_order' => 20],
            ['name' => 'raw_materials', 'display_name' => 'Bahan Baku', 'category' => 'Inventory', 'icon' => 'layers', 'route_name' => 'raw-materials.index', 'sort_order' => 21],
            ['name' => 'suppliers', 'display_name' => 'Pemasok', 'category' => 'Inventory', 'icon' => 'truck', 'route_name' => 'raw-materials.suppliers.index', 'sort_order' => 22],
            ['name' => 'production', 'display_name' => 'Produksi', 'category' => 'Inventory', 'icon' => 'settings', 'route_name' => 'production.index', 'sort_order' => 23],
            ['name' => 'stock_opname', 'display_name' => 'Stock Opname', 'category' => 'Inventory', 'icon' => 'clipboard-list', 'route_name' => 'stock-opname.index', 'sort_order' => 24],
            ['name' => 'stock_transfer', 'display_name' => 'Transfer Stok', 'category' => 'Inventory', 'icon' => 'repeat', 'route_name' => 'stock-transfers.index', 'sort_order' => 25],

            // CRM Category
            ['name' => 'customer_management', 'display_name' => 'Manajemen Pelanggan', 'category' => 'CRM', 'icon' => 'users', 'route_name' => 'customer-debts.customers', 'sort_order' => 30],

            // Operations Category
            ['name' => 'table_management', 'display_name' => 'Manajemen Meja', 'category' => 'Operations', 'icon' => 'grid', 'route_name' => 'tables.index', 'sort_order' => 40],
            ['name' => 'task_management', 'display_name' => 'Manajemen Tugas', 'category' => 'Operations', 'icon' => 'check-square', 'route_name' => 'tasks.index', 'sort_order' => 41],

            // HR Category
            ['name' => 'employee_management', 'display_name' => 'Manajemen Karyawan', 'category' => 'HR', 'icon' => 'user-check', 'route_name' => 'employees.index', 'sort_order' => 50],
            ['name' => 'access_rights', 'display_name' => 'Hak Akses', 'category' => 'HR', 'icon' => 'shield', 'route_name' => 'employees.permissions', 'sort_order' => 51],

            // Reports Category
            ['name' => 'dashboard', 'display_name' => 'Dashboard & Statistik', 'category' => 'Reports', 'icon' => 'bar-chart', 'route_name' => 'dashboard', 'sort_order' => 60],
            ['name' => 'reports', 'display_name' => 'Laporan Lengkap', 'category' => 'Reports', 'icon' => 'file-bar-chart', 'route_name' => 'reports.index', 'sort_order' => 61],

            // Settings Category
            ['name' => 'payment_methods', 'display_name' => 'Metode Pembayaran', 'category' => 'Settings', 'icon' => 'credit-card', 'route_name' => 'outlet-payment-links.index', 'sort_order' => 70],
            ['name' => 'outlet_policies', 'display_name' => 'Kebijakan Outlet', 'category' => 'Settings', 'icon' => 'file-text', 'route_name' => 'outlet-policies.index', 'sort_order' => 71],
            ['name' => 'account_settings', 'display_name' => 'Pengaturan Akun', 'category' => 'Settings', 'icon' => 'user-cog', 'route_name' => 'profile.edit', 'sort_order' => 72],
            ['name' => 'multi_outlet', 'display_name' => 'Multi-Outlet', 'category' => 'Settings', 'icon' => 'building', 'route_name' => 'outlets.index', 'sort_order' => 73],

            // Support Category
            ['name' => 'help_faq', 'display_name' => 'Bantuan & FAQ', 'category' => 'Support', 'icon' => 'help-circle', 'route_name' => 'faq.index', 'sort_order' => 80],

            // Premium Category
            ['name' => 'landing_page', 'display_name' => 'Landing Page Builder', 'category' => 'Premium', 'icon' => 'globe', 'route_name' => 'landing-page.index', 'sort_order' => 90],
            ['name' => 'testimonials', 'display_name' => 'Manajemen Testimoni', 'category' => 'Premium', 'icon' => 'star', 'route_name' => 'testimonials.index', 'sort_order' => 91],
            ['name' => 'ai_insights', 'display_name' => 'AI Insights', 'category' => 'Premium', 'icon' => 'brain', 'route_name' => 'ai-insights.index', 'sort_order' => 92],
            ['name' => 'clara_ai', 'display_name' => 'Clara AI Assistant', 'category' => 'Premium', 'icon' => 'message-circle', 'route_name' => 'claude-ai.chat', 'sort_order' => 93],
            ['name' => 'reseller_app', 'display_name' => 'Aplikasi Reseller', 'category' => 'Premium', 'icon' => 'share-2', 'route_name' => 'reseller-applications.index', 'sort_order' => 94],
            ['name' => 'opportunity_map', 'display_name' => 'Peta Cuan Lokasi', 'category' => 'Premium', 'icon' => 'map-pin', 'route_name' => 'opportunity-map.index', 'sort_order' => 95],
            ['name' => 'video_ai', 'display_name' => 'Video Prompt AI', 'category' => 'Premium', 'icon' => 'video', 'route_name' => 'clara-ai.video-prompt', 'sort_order' => 96],
            ['name' => 'script_ai', 'display_name' => 'Script Generator AI', 'category' => 'Premium', 'icon' => 'file-text', 'route_name' => 'clara-ai.affiliate-script', 'sort_order' => 97],
            ['name' => 'image_ai', 'display_name' => 'Image Prompt AI', 'category' => 'Premium', 'icon' => 'image', 'route_name' => 'clara-ai.ads-image-prompt', 'sort_order' => 98],
            ['name' => 'kalkulaba_ai', 'display_name' => 'Kalkulaba AI', 'category' => 'Premium', 'icon' => 'calculator', 'route_name' => 'clara-ai.kalkulaba', 'sort_order' => 99],
            ['name' => 'reseller_products', 'display_name' => 'Produk Reseller', 'category' => 'Premium', 'icon' => 'box', 'route_name' => 'reseller-products.index', 'sort_order' => 100],
            ['name' => 'track_transaction_location', 'display_name' => 'Track Lokasi Transaksi', 'category' => 'Premium', 'icon' => 'map-pin', 'route_name' => null, 'sort_order' => 101],
        ];

        foreach ($features as $feature) {
            $existing = Feature::where('name', $feature['name'])->first();

            if ($existing) {
                // Keep existing is_active status
                $existing->update(array_merge($feature, [
                    'description' => null,
                ]));
            } else {
                Feature::create(array_merge($feature, [
                    'description' => null,
                    'is_active' => true,
                ]));
            }
        }
    }
}
