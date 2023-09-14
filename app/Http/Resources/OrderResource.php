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
                'id' => $product->id,
                'name' => $product->name,
                'name_ar' => $product->name_ar,
                'description' => $product->description,
                'description_ar' => $product->description_ar,
                'price' => $product->price,
                'image' => url($product->image),
                'estimated_time' => $product->estimated_time,
                'status' => $product->status,
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
