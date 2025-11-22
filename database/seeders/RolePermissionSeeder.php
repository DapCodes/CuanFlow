<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view_dashboard', 'view_analytics',
            // Products
            'view_products', 'create_products', 'edit_products', 'delete_products',
            // Raw Materials
            'view_raw_materials', 'create_raw_materials', 'edit_raw_materials', 'delete_raw_materials',
            // Stock
            'view_stock', 'adjust_stock', 'transfer_stock', 'view_stock_history',
            // Recipes & HPP
            'view_recipes', 'create_recipes', 'edit_recipes', 'delete_recipes', 'calculate_hpp', 'view_hpp_history',
            // Productions
            'view_productions', 'create_productions', 'edit_productions', 'complete_productions',
            // POS
            'access_pos', 'apply_discount', 'void_transaction', 'reprint_receipt',
            // Sales
            'view_sales', 'view_all_sales', 'export_sales', 'refund_sales',
            // Customers
            'view_customers', 'create_customers', 'edit_customers', 'delete_customers', 'manage_customer_debt',
            // Purchases
            'view_purchases', 'create_purchases', 'edit_purchases', 'receive_purchases', 'delete_purchases',
            // Suppliers
            'view_suppliers', 'create_suppliers', 'edit_suppliers', 'delete_suppliers',
            // Expenses
            'view_expenses', 'create_expenses', 'edit_expenses', 'approve_expenses', 'delete_expenses',
            // Users & Roles
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
            // Outlets
            'view_outlets', 'create_outlets', 'edit_outlets', 'delete_outlets', 'view_all_outlets',
            // Reports
            'view_reports', 'export_reports', 'schedule_reports',
            // Settings
            'view_settings', 'edit_settings',
            // Backup
            'create_backup', 'restore_backup', 'view_backup_logs',
            // AI
            'access_ai_chatbot', 'view_ai_insights',
            // Activity Log
            'view_activity_log',
        ];

        foreach ($permissions as $p) Permission::create(['name' => $p]);

        // Owner - Full Access
        Role::create(['name' => 'owner'])->givePermissionTo(Permission::all());

        // Admin
        Role::create(['name' => 'admin'])->givePermissionTo([
            'view_dashboard', 'view_analytics',
            'view_products', 'create_products', 'edit_products', 'delete_products',
            'view_raw_materials', 'create_raw_materials', 'edit_raw_materials', 'delete_raw_materials',
            'view_stock', 'adjust_stock', 'transfer_stock', 'view_stock_history',
            'view_recipes', 'create_recipes', 'edit_recipes', 'delete_recipes', 'calculate_hpp', 'view_hpp_history',
            'view_productions', 'create_productions', 'edit_productions', 'complete_productions',
            'access_pos', 'apply_discount', 'void_transaction', 'reprint_receipt',
            'view_sales', 'view_all_sales', 'export_sales', 'refund_sales',
            'view_customers', 'create_customers', 'edit_customers', 'delete_customers', 'manage_customer_debt',
            'view_purchases', 'create_purchases', 'edit_purchases', 'receive_purchases', 'delete_purchases',
            'view_suppliers', 'create_suppliers', 'edit_suppliers', 'delete_suppliers',
            'view_expenses', 'create_expenses', 'edit_expenses', 'approve_expenses', 'delete_expenses',
            'view_users', 'create_users', 'edit_users',
            'view_outlets', 'view_reports', 'export_reports', 'schedule_reports',
            'view_settings', 'edit_settings', 'access_ai_chatbot', 'view_ai_insights', 'view_activity_log',
        ]);

        // Kasir
        Role::create(['name' => 'kasir'])->givePermissionTo([
            'view_dashboard', 'view_products', 'view_stock',
            'access_pos', 'reprint_receipt', 'view_sales',
            'view_customers', 'create_customers', 'edit_customers', 'manage_customer_debt',
        ]);

        // Produksi
        Role::create(['name' => 'produksi'])->givePermissionTo([
            'view_dashboard', 'view_products', 'view_raw_materials',
            'view_stock', 'adjust_stock', 'view_stock_history', 'view_recipes',
            'view_productions', 'create_productions', 'edit_productions', 'complete_productions',
        ]);
    }
}