<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RemoveIngredientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->ingredient->id,
            'name' => $this->ingredient->name,
            'name_ar' => $this->ingredient->name_ar,
            'quantity' => $this->quantity
        ];
    }
}
