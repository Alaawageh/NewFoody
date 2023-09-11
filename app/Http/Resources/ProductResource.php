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
            'extraIng' => $this->extraIng,
            'ingredient' => $this->ingredient,
            'category' => CategoryResource::make($this->category)
        ];
    }
}
