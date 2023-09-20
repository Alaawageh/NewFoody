<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RateServiceResource extends JsonResource
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
                'subTotal' => $prod->subTotal,
                'rating' => $pro->rating()->first()
            ];
            


            
            $products[] = $productData;
        
        }
        return $products;

    }
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'serviceRate' => $this->serviceRate,
            'feedback' => $this->feedback,
            'products' => $this->withProductsAndExtra($this->resource),
            'total_price' => $this->total_price,
            'estimatedForOrder' => $this->estimatedForOrder,
            'table' => TableResource::make($this->table),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at   
            
        ];
    }
}
