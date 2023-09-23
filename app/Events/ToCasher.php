<?php

namespace App\Events;

use App\Http\Resources\OrderProductResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToCasher implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    public function broadcastOn()
    {
        return new Channel('Casher.'.$this->order->branch->id);
    }

    public function broadcastWith()
    {
        return [
            'Casher' => new OrderProductResource($this->order),
        ];
    }

    public function broadcastAs()
    {
        return 'ToCasher';
    }

}
