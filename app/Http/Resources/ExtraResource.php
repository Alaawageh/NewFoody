<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name' => $this->ingredient->name,
            // 'ingredient_id' => $this->ingredient->id,
            'quantity' => $this->pivot->quantity,
            'price_per_piece' => $this->pivot->price_per_piece,
            'price_per_kilo' =>$this->price_per_kilo,
            'branch' => $this->branch,
        ];
    }
}
