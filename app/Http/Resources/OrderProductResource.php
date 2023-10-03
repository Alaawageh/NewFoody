<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
use App\Models\OrderProductExtraIngredient;
use App\Models\Product;
use App\Models\ProductExtraIngredient;
use App\Models\ProductIngredient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    public function withProductsAndExtra($order)
    {
        $products = [];
        foreach ($order->products as $product) {
            $prods = Product::where('id',$product->product_id)->get();
            foreach($prods as $pro) {
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
                    'qty' => $product['qty'],
                    'note' => $product['note'],
                    'subTotal' => $product['subTotal']
                ];
            }

            if(isset($product->ingredients)){

                $removeIngredient = [];
                foreach ($product->ingredients as $Ingredient) {
                    
                    $IngredientData = [
                        'id' => $Ingredient->id,
                        'name' => $Ingredient->name,
                        
                    ];
                    $removeIngredient[] = $IngredientData;
                    
                }
                
                $productData['removeIngredient'] = $removeIngredient;
            }
            if(isset($product->extra)){
                $xx = [];
                foreach ($product->extra as $extraIngredient) {
                    $price_by_peice = ProductExtraIngredient::where('product_id',$pro->id)->where('extra_ingredient_id',$extraIngredient->id)->first();

                    if($price_by_peice) {
                        $extraIngredientData = [
                            'id' => $extraIngredient->id,
                            'name' => $extraIngredient->ingredient->name,
                            'price_per_piece' => $price_by_peice->price_per_piece,
                        ];
                    }else{
                        $extraIngredientData = [
                            'id' => $extraIngredient->id,
                            'name' => $extraIngredient->ingredient->name,
                        ];
                    }
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
            'time_start' => Carbon::parse($this->time_start)->format("Y-m-d H:i:s"),
            'time_end' => Carbon::parse($this->time_end)->format("Y-m-d H:i:s"),
            'time_Waiter' => Carbon::parse($this->time_Waiter)->format("Y-m-d H:i:s"),
            'estimatedForOrder' => $this->estimatedForOrder,
            'products' =>$this->withProductsAndExtra($this->resource),
            'total_price' => $this->total_price,
            'table' => TableResource::make($this->table),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
