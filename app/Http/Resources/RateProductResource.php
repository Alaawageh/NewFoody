<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RateProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'product' => ProductResource::make($this->product),
            'Rate_product'=> $this->RateProduct,
        ];
    }
}
