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
                // 'price' => $product->price,
                'qty' => $product->pivot->qty,
                'note' => $product->pivot->note,
            ];
            
            if($product->extraIngredients){
                $xx = [];
                foreach ($product->extraIngredients as $extraIngredient) {
                    $extraIngredientData = [
                        'name' => $extraIngredient->name,
                        // 'price_per_piece' => $extraIngredient->price_per_peice,
                        // 'totalItem' => ($product['price'] * $productData['qty']) + $extraIngredient['price_per_peice']
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
            'table' => TableResource::make($this->table),
            'branch' => BranchesResource::make($this->branch),
            'products' => $this->withProductsAndExtra($this->resource)
            
        ];
    }
}
