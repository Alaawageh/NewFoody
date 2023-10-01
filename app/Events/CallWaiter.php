<?php

namespace App\Events;

use App\Http\Resources\TableResource;
use App\Models\Branch;
use App\Models\Table;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallWaiter implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $table;
    public $branch;

    public function __construct($table,$branch)
    {
        $this->table = $table;
        $this->branch = $branch;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('waiter.'.$this->branch->id),
        ];
    }
    public function broadcastWith()
    {

        return [
            'message' => 'Can you help me?',
            'table' => new TableResource($this->table),

        ];
    }

    public function broadcastAs()
    {
        return 'CallWaiter';
    }
}
