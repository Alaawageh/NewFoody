<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function withProductsAndExtra($order)
    {
        $products = [];
        foreach ($order->products as $product) {
            $productData = [
                'name' => $product->name,
                'qty' => $product->pivot->qty,
                'note' => $product->pivot->note,
            ];
            
            if(isset($product->extra)){
                $xx = [];
                foreach ($product->extra as $extraIngredient) {
                    $extraIngredientData = [
                        'name' => $extraIngredient->name,
                    ];
                    
                    $xx[] = $extraIngredientData;
                    
                }
                $productData['extra'] = $xx;
            }

            
            $products[] = $productData;
        
        }
        return $products;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'is_update' => $this->is_update,
            'time' => $this->time,
            'total_price' => $this->total_price,
            'table' => TableResource::make($this->table),
            'products' => $this->withProductsAndExtra($this->resource)
            
        ];
    }
}
