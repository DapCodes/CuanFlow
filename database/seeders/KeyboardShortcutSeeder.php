<?php

namespace Database\Seeders;

use App\Models\KeyboardShortcut;
use Illuminate\Database\Seeder;

class KeyboardShortcutSeeder extends Seeder
{
    public function run(): void
    {
        $shortcuts = [
            ['action' => 'pay_cash', 'shortcut' => 'F1', 'description' => 'Bayar Tunai', 'context' => 'pos'],
            ['action' => 'pay_qris', 'shortcut' => 'F2', 'description' => 'Bayar QRIS', 'context' => 'pos'],
            ['action' => 'new_transaction', 'shortcut' => 'F3', 'description' => 'Transaksi Baru', 'context' => 'pos'],
            ['action' => 'search_product', 'shortcut' => 'F4', 'description' => 'Cari Produk', 'context' => 'pos'],
            ['action' => 'search_customer', 'shortcut' => 'F5', 'description' => 'Cari Pelanggan', 'context' => 'pos'],
            ['action' => 'apply_discount', 'shortcut' => 'F6', 'description' => 'Terapkan Diskon', 'context' => 'pos'],
            ['action' => 'hold_transaction', 'shortcut' => 'F7', 'description' => 'Tahan Transaksi', 'context' => 'pos'],
            ['action' => 'recall_transaction', 'shortcut' => 'F8', 'description' => 'Ambil Transaksi', 'context' => 'pos'],
            ['action' => 'void_item', 'shortcut' => 'Delete', 'description' => 'Hapus Item', 'context' => 'pos'],
            ['action' => 'quantity_up', 'shortcut' => '+', 'description' => 'Tambah Qty', 'context' => 'pos'],
            ['action' => 'quantity_down', 'shortcut' => '-', 'description' => 'Kurang Qty', 'context' => 'pos'],
            ['action' => 'focus_barcode', 'shortcut' => 'Ctrl+B', 'description' => 'Focus Barcode', 'context' => 'pos'],
            ['action' => 'save', 'shortcut' => 'Ctrl+S', 'description' => 'Simpan', 'context' => 'general'],
            ['action' => 'search', 'shortcut' => 'Ctrl+F', 'description' => 'Cari', 'context' => 'general'],
            ['action' => 'new', 'shortcut' => 'Ctrl+N', 'description' => 'Baru', 'context' => 'general'],
            ['action' => 'refresh', 'shortcut' => 'Ctrl+R', 'description' => 'Refresh', 'context' => 'general'],
        ];
        foreach ($shortcuts as $s) KeyboardShortcut::create(array_merge($s, ['user_id' => null]));
    }
}