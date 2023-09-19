<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
use App\Models\OrderProductExtraIngredient;
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    public function withProductsAndExtra($order)
    {
        $products = [];
        foreach ($order->products as $product) {
            $pro = Product::where('id',$product->product_id)->first();
            $prod = OrderProduct::where('order_id',$order->id)->where('product_id',$pro->id)->first();
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
                'qty' => $prod->qty,
                'note' => $prod->note,
                'subTotal' => $prod->subTotal
            ];
            
            if(isset($product->extra)){
                $xx = [];
                foreach ($product->extra as $extraIngredient) {
                    $price_by_peice = ProductExtraIngredient::where('product_id',$pro->id)->where('extra_ingredient_id',$extraIngredient->id)->first();

                    $extraIngredientData = [
                        'id' => $extraIngredient->id,
                        'name' => $extraIngredient->name,
                        'price_per_piece' => $price_by_peice->price_per_piece,
                    ];
                    
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
            'id' => $this->id,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'is_update' => $this->is_update,
            'time' => $this->time,
            'time_start' => $this->time_start,
            'time_end' => $this->time_end,
            'time_Waiter' => $this->time_Waiter,
            'estimatedForOrder' => $this->estimatedForOrder,
            'products' =>$this->withProductsAndExtra($this->resource),
            'total_price' => $this->total_price,
            'table' => TableResource::make($this->table),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
