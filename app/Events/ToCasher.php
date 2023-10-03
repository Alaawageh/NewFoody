<?php

namespace App\Events;

use App\Http\Resources\BillResource;
use App\Http\Resources\OrderResource;
use App\Models\Bill;
use App\Models\Branch;
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

    public $bill;
    public $branch;

    public function __construct(Bill $bill,Branch $branch)
    {
        $this->bill = $bill;
        $this->branch = $branch;
    }
    public function broadcastOn()
    {
        return new Channel('Casher.'.$this->branch->id);
    }

    public function broadcastWith()
    {
        return [
            'Casher' => new BillResource($this->bill),
        ];
    }

    public function broadcastAs()
    {
        return 'ToCasher';
    }

}
