<?php

namespace App\Events;

use App\Http\Resources\IngredientResource;
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

    public $ingredient;

    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }

    public function broadcastOn()
    {
        return new Channel('ingredient.'.$this->ingredient->branch->id);

    }

    public function broadcastWith()
    {
        
        return [
            'ingredient' =>new IngredientResource($this->ingredient),
            'Opps The ingredient is out of stock'
        ];
    }

    public function broadcastAs()
    {
        return 'IngredientMin';
    }
}
