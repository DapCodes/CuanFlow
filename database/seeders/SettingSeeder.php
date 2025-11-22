<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'app_name', 'value' => 'UMKM POS', 'type' => 'string'],
            ['group' => 'general', 'key' => 'currency', 'value' => 'IDR', 'type' => 'string'],
            ['group' => 'general', 'key' => 'currency_symbol', 'value' => 'Rp', 'type' => 'string'],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Asia/Jakarta', 'type' => 'string'],
            ['group' => 'pos', 'key' => 'tax_enabled', 'value' => 'false', 'type' => 'boolean'],
            ['group' => 'pos', 'key' => 'tax_percent', 'value' => '11', 'type' => 'float'],
            ['group' => 'pos', 'key' => 'receipt_header', 'value' => 'Terima Kasih', 'type' => 'string'],
            ['group' => 'pos', 'key' => 'receipt_footer', 'value' => 'Barang yang sudah dibeli tidak dapat dikembalikan', 'type' => 'string'],
            ['group' => 'pos', 'key' => 'auto_print_receipt', 'value' => 'true', 'type' => 'boolean'],
            ['group' => 'midtrans', 'key' => 'is_production', 'value' => 'false', 'type' => 'boolean'],
            ['group' => 'midtrans', 'key' => 'merchant_id', 'value' => '', 'type' => 'string'],
            ['group' => 'midtrans', 'key' => 'client_key', 'value' => '', 'type' => 'string'],
            ['group' => 'midtrans', 'key' => 'server_key', 'value' => '', 'type' => 'string'],
            ['group' => 'notification', 'key' => 'low_stock_alert', 'value' => 'true', 'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'expiry_alert_days', 'value' => '7', 'type' => 'integer'],
            ['group' => 'notification', 'key' => 'email_notifications', 'value' => 'true', 'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'wa_notifications', 'value' => 'false', 'type' => 'boolean'],
            ['group' => 'ai', 'key' => 'provider', 'value' => 'openai', 'type' => 'string'],
            ['group' => 'ai', 'key' => 'api_key', 'value' => '', 'type' => 'string'],
            ['group' => 'ai', 'key' => 'model', 'value' => 'gpt-3.5-turbo', 'type' => 'string'],
            ['group' => 'backup', 'key' => 'auto_backup', 'value' => 'true', 'type' => 'boolean'],
            ['group' => 'backup', 'key' => 'backup_frequency', 'value' => 'daily', 'type' => 'string'],
            ['group' => 'backup', 'key' => 'backup_time', 'value' => '02:00', 'type' => 'string'],
            ['group' => 'backup', 'key' => 'retention_days', 'value' => '30', 'type' => 'integer'],
        ];
        foreach ($settings as $s) Setting::create(array_merge($s, ['outlet_id' => null]));
    }
}