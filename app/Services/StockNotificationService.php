<?php

namespace App\Services;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\StockNotification;
use Carbon\Carbon;

class StockNotificationService
{
    /**
     * Check all stock conditions for a specific outlet.
     */
    public function checkAllStock(?int $outletId): void
    {
        if (! $outletId) {
            return;
        }

        $this->checkProducts($outletId);
        $this->checkRawMaterials($outletId);
    }

    /**
     * Check Product stock levels and expiry.
     */
    private function checkProducts(int $outletId): void
    {
        $products = Product::where('outlet_id', $outletId)
            ->where('track_stock', true)
            ->where('is_stock', true) // Only check products marked as stock
            ->with(['stocks' => function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            }])
            ->get();

        foreach ($products as $product) {
            $stock = $product->stocks->first();
            $quantity = $stock ? $stock->quantity : 0;
            $minStock = $product->min_stock;

            // Check Low/Out of Stock
            if ($quantity <= 0) {
                $this->createNotification($outletId, $product, 'out_of_stock', "Stok Habis: {$product->name}", "Stok produk {$product->name} telah habis.", $quantity, $minStock);
            } elseif ($minStock > 0 && $quantity <= $minStock) {
                $this->createNotification($outletId, $product, 'low_stock', "Stok Menipis: {$product->name}", "Stok produk {$product->name} hampir habis. Sisa: {$quantity}", $quantity, $minStock);
            }

            $this->checkProductExpiry($outletId, $product);
        }
    }

    /**
     * Check for expiring or expired product batches from production.
     */
    private function checkProductExpiry(int $outletId, Product $product): void
    {
        $today = Carbon::today();
        $soon = Carbon::today()->addDays(7);

        // Check Production for batches with expiry dates that still have stock (actual_quantity)
        $expiringBatches = \App\Models\Production::where('product_id', $product->id)
            ->where('status', 'completed')
            ->where('is_disposed', false)
            ->whereNotNull('expired_at')
            ->whereBetween('expired_at', [$today, $soon])
            ->get();

        foreach ($expiringBatches as $batch) {
            $days = $today->diffInDays($batch->expired_at);
            $this->createNotification($outletId, $product, 'expiring_soon', "Produk Hampir Kadaluarsa: {$product->name}", "Batch {$batch->batch_number} produk {$product->name} akan kadaluarsa dalam {$days} hari ({$batch->expired_at->format('d M Y')}).", $batch->actual_quantity, null);
        }

        $expiredBatches = \App\Models\Production::where('product_id', $product->id)
            ->where('status', 'completed')
            ->where('is_disposed', false)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', $today)
            ->get();

        foreach ($expiredBatches as $batch) {
            $this->createNotification($outletId, $product, 'expired', "Produk Kadaluarsa: {$product->name}", "Batch {$batch->batch_number} produk {$product->name} sudah kadaluarsa sejak {$batch->expired_at->format('d M Y')}.", $batch->actual_quantity, null);
        }
    }

    /**
     * Check RawMaterial stock levels and expiry.
     */
    private function checkRawMaterials(int $outletId): void
    {
        $materials = RawMaterial::where('outlet_id', $outletId)
            ->with(['stocks' => function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            }])
            ->get();

        foreach ($materials as $material) {
            $stock = $material->stocks->first();
            $quantity = $stock ? $stock->quantity : 0;
            $minStock = $material->min_stock;

            if ($quantity <= 0) {
                $this->createNotification($outletId, $material, 'out_of_stock', "Bahan Habis: {$material->name}", "Stok bahan baku {$material->name} telah habis.", $quantity, $minStock);
            } elseif ($minStock > 0 && $quantity <= $minStock) {
                $this->createNotification($outletId, $material, 'low_stock', "Bahan Menipis: {$material->name}", "Stok bahan baku {$material->name} hampir habis. Sisa: {$quantity}", $quantity, $minStock);
            }

            $this->checkRawMaterialExpiry($outletId, $material);
        }
    }

    /**
     * Check for expiring or expired raw materials batches.
     */
    private function checkRawMaterialExpiry(int $outletId, RawMaterial $material): void
    {
        $today = Carbon::today();
        $soon = Carbon::today()->addDays(7);

        // Check PurchaseItem for batches with expiry dates
        $expiringBatches = \App\Models\PurchaseItem::where('raw_material_id', $material->id)
            ->where('remaining_quantity', '>', 0)
            ->whereNotNull('expired_at')
            ->whereBetween('expired_at', [$today, $soon])
            ->get();

        foreach ($expiringBatches as $batch) {
            $days = $today->diffInDays($batch->expired_at);
            $this->createNotification($outletId, $material, 'expiring_soon', "Bahan Hampir Kadaluarsa: {$material->name}", "Batch {$batch->batch_number} bahan {$material->name} akan kadaluarsa dalam {$days} hari ({$batch->expired_at->format('d M Y')}).", $batch->remaining_quantity, null);
        }

        $expiredBatches = \App\Models\PurchaseItem::where('raw_material_id', $material->id)
            ->where('remaining_quantity', '>', 0)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', $today)
            ->get();

        foreach ($expiredBatches as $batch) {
            $this->createNotification($outletId, $material, 'expired', "Bahan Kadaluarsa: {$material->name}", "Batch {$batch->batch_number} bahan {$material->name} sudah kadaluarsa sejak {$batch->expired_at->format('d M Y')}.", $batch->remaining_quantity, null);
        }
    }

    /**
     * Create notification if not already exists (unread globally).
     * Note: Implements multi-user read logic via pivot table.
     */
    private function createNotification(int $outletId, $model, string $type, string $title, string $message, $currentStock, $minStock): void
    {
        // Check if an unread notification for this specific item and type already exists
        // We consider it "unread" if it's not marked as is_read on the main table.
        // Once marked is_read = true, we can create a new one if needed.
        $exists = StockNotification::where('outlet_id', $outletId)
            ->where('stockable_id', $model->id)
            ->where('stockable_type', get_class($model))
            ->where('type', $type)
            ->where('is_read', false)
            ->exists();

        if (! $exists) {
            StockNotification::create([
                'outlet_id' => $outletId,
                'stockable_id' => $model->id,
                'stockable_type' => get_class($model),
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'current_stock' => $currentStock,
                'min_stock' => $minStock,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Get recent notifications for an outlet, including read status for current user.
     */
    public function getLatestNotifications(?int $outletId, int $limit = 10)
    {
        if (! $outletId) {
            return collect();
        }

        $userId = auth()->id();

        $notifications = StockNotification::where('outlet_id', $outletId)
            ->where('is_read', false) // Still filter by globally "active" notifications
            ->with(['readByUsers' => function ($q) {
                $q->select('users.id', 'users.name', 'avatar'); // For avatars
            }])
            ->latest()
            ->limit($limit)
            ->get();

        // Map read status for current user
        foreach ($notifications as $notification) {
            $notification->is_read_by_me = $notification->readByUsers->contains('id', $userId);
        }

        return $notifications;
    }

    /**
     * Get the count of unread notifications for a user in an outlet.
     */
    public function getUnreadCount(?int $outletId): int
    {
        if (! $outletId) {
            return 0;
        }

        $userId = auth()->id();

        return StockNotification::where('outlet_id', $outletId)
            ->where('is_read', false)
            ->unreadBy($userId)
            ->count();
    }
}
