<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'price' => $this->price,
            'position' => $this->position,
            'image' => url($this->image),
            'estimated_time' => $this->estimated_time,
            'status' => $this->status,
            'category' => $this->category,
            'branch' => $this->branch,
            'ingredients' => $this->ingredients,
            'extra_ingredients' => ExtraResource::collection($this->extraIngredients),
            'AvgRating' => round($this->rating->avg('value'),2)
        ];
    
    }
}
