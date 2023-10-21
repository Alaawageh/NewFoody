<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\ProductExtraIngredient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
                    'qty' => $product['qty'],
                    'note' => $product['note']
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
                    
                    $productExtra = ProductExtraIngredient::where('product_id',$pro->id)->where('extra_ingredient_id',$extraIngredient->id)->first();
                    if($productExtra) {
                        $extraIngredientData = [
                            'id' => $extraIngredient->id,
                            'name' => $extraIngredient->ingredient->name,
                            'quantity' => $productExtra->quantity,
                            'price_per_piece' => $productExtra->price_per_piece,
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
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'table' => $this->table->table_num,
            'takeaway' => $this->takeaway,
            'status' => $this->status,
            'time' => $this->time,
            'products' => $this->withProductsAndExtra($this->resource),
            'time_start' => Carbon::parse($this->time_start)->format("Y-m-d H:i:s"),
            'time_end' => Carbon::parse($this->time_end)->format("Y-m-d H:i:s"),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at 
        ];
    }
}
