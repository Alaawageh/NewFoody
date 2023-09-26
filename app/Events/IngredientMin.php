<?php

namespace App\Events;

use App\Http\Resources\IngredientResource;
use App\Models\Branch;
use App\Models\Ingredient;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IngredientMin implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lowIngredients;
    public $branch;

    public function __construct(array $lowIngredients, Branch $branch)
    {
        $this->lowIngredients = $lowIngredients;
        $this->branch = $branch;
    }

    public function broadcastOn()
    {
        return new Channel('ingredient.'.$this->branch->id);

    }

    public function broadcastWith()
    {
        
        return [
            'ingredient' => $this->lowIngredients,
            'Opps The ingredient is out of stock'
        ];
    }

    public function broadcastAs()
    {
        return 'IngredientMin';
    }
}
