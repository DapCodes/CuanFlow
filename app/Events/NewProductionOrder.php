<?php

namespace App\Events;

use App\Models\Sale;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewProductionOrder implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $orderData;
    public string $type;

    public function __construct(Sale $sale, string $type = 'new-order')
    {
        $this->type = $type;
        $sale->loadMissing(['items.product.unit', 'items.product.defaultRecipe', 'customer', 'table']);

        // Only include non-stock items (items that need production)
        $pendingItems = $sale->items->filter(function ($item) {
            return $item->product && !$item->product->is_stock && $item->production_status === 'pending';
        });

        $this->orderData = [
            'sale_id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'customer_name' => $sale->customer->name ?? 'Pelanggan Umum',
            'table_name' => $sale->table->name ?? null,
            'created_at' => $sale->created_at->format('H:i'),
            'created_at_human' => $sale->created_at->diffForHumans(),
            'timestamp' => $sale->created_at->timestamp,
            'items_count' => $pendingItems->count(),
            'items' => $pendingItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => (int) $item->quantity,
                    'unit' => $item->product->unit->name ?? 'Pcs',
                    'notes' => $item->notes,
                    'has_recipe' => $item->product->defaultRecipe !== null,
                ];
            })->values()->toArray(),
        ];
    }

    public function broadcastOn(): array
    {
        // Use the outlet_id from the sale to broadcast on the correct channel
        $outletId = \App\Models\Sale::find($this->orderData['sale_id'])?->outlet_id;

        return [
            new Channel('production.outlet.' . $outletId),
        ];
    }

    public function broadcastAs(): string
    {
        return $this->type;
    }
}
