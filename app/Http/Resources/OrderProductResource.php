<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    public function withProductsAndExtra($order)
    {
        $products = [];
        foreach ($order->products as $product) {
            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => $product->pivot->qty,
                'note' => $product->pivot->note,
                'subTotal' => $product->pivot->subTotal,
            ];
            
            if($product->extraIngredients){
                $xx = [];
                foreach ($product->extraIngredients as $extraIngredient) {
                    $extraIngredientData = [
                        'name' => $extraIngredient->name,
                        'price_per_piece' => $extraIngredient->price_per_peice,                    ];
                    
                    $xx[] = $extraIngredientData;
                    
                }
                $productData['extra'] = $xx;
            }

            
            $products[] = $productData;
        
        }
        return $products;
    }
    public function toArray(Request $request)
    {
        return [
            'products' =>$this->withProductsAndExtra($this->resource),
            'total_price' => $this->total_price,
            'table' => TableResource::make($this->table),
            'branch' => BranchesResource::make($this->branch),
        ];
    }
}
