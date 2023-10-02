<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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
            'price' => $this->price,
            'is_paid' => $this->is_paid,
            'order' => OrderProductResource::collection($this->order)->map(function ($order) {
                return $order->status === 3 ? $order : "There is another order befor preparing for the same table in the kitchen ";
            }),
        ];
    }
}
