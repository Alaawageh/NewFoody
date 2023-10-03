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
            'price' => $this->order->where('status', 3)->sum('total_price'),
            'is_paid' => $this->is_paid,
            'order' => OrderProductResource::collection($this->order->filter(function ($order) {
                return $order->status === 3;
            })),
        ];
    }
}
