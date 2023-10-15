<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ingredient' => $this->ingredient,
            'price_per_piece' =>$this->pivot->price_per_piece,
            'quantity' =>$this->pivot->quantity,
            'unit' => $this->pivot->unit,
            'branch' => $this->branch
        ];
    }
}
