<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestructionResource extends JsonResource
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
            'name' => $this->ingredient->name,
            'name_ar' => $this->ingredient->name_ar,
            'Desturcted_amount' => $this->qty,
            'unit_Destructed' => $this->unit,
            'total_quantity' => $this->ingredient->total_quantity,
            'unit' =>  $this->ingredient->unit,
            'threshold' => $this->ingredient->threshold,
            'created_at' => $this->created_at,
        ];
    }
}
