<?php

namespace App\Events;

use App\Models\Sale;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionOrderRefunded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $orderData;
    public string $type;

    public function __construct(Sale $sale, string $type = 'order-refunded')
    {
        $this->type = $type;
        $this->orderData = [
            'sale_id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
        ];
    }

    public function broadcastOn(): array
    {
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
