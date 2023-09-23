<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddOrderResource extends JsonResource
{
    public function withProductsAndExtra($order)
    {
        $products = [];
        foreach ($order->products as $product) {
            $pro = Product::where('id',$product->product_id)->first();
            $productData = [
                'id' => $pro->id,
                'name' => $pro->name,
                'name_ar' => $pro->name_ar,
                'description' => $pro->description,
                'description_ar' => $pro->description_ar,
                'price' => $pro->price,
                'image' => url($pro->image),
                'estimated_time' => $pro->estimated_time,
                'status' => $pro->status,
            ];
            
            if(isset($product->extra)){
                $xx = [];
                foreach ($product->extra as $extraIngredient) {
                    $extraIngredientData = [
                        'name' => $extraIngredient->ingredient->name,
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
            'estimatedForOrder' => $this->estimatedForOrder,
            'table' => TableResource::make($this->table),
            'products' => $this->withProductsAndExtra($this->resource)
            
        ];
    }
}
