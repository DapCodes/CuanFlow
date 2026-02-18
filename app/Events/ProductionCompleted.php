<?php

namespace App\Events;

use App\Models\Sale;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $orderData;

    public function __construct(Sale $sale)
    {
        $this->orderData = [
            'sale_id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'outlet_id' => $sale->outlet_id,
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('production.outlet.'.$this->orderData['outlet_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'production-completed';
    }
}
