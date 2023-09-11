<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'status' , 'total_price' , 'is_paid' , 'is_update' ,'time',
        'time_end' , 'time_Waiter' , 'products' , 'table_id' , 'branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function table()
    {
        return $this->belongsTo(Table::class);
    }


    protected $casts = [
        'products' => 'array',
  
    ];
    public function calculate($request,$order)
    {
        $totalPrice = 0;
        foreach($request->products as $productData) {
            $product = Product::find($productData['id']);

            $productSubtotal = $product->price * $productData['qty'];

            $totalPrice += $productSubtotal;

            if(isset($productData['extraIng'])) {

                foreach($productData['extraIng'] as $ingredientData) {
                    $ingredient = ExtraIngredient::find($ingredientData['id']);

                    $totalPrice += $ingredient->price_per_piece;
                }
            }
        }
        $orderTax = intval($order->branch->taxRate) / 100;
        
        return $total_price = $totalPrice + ($totalPrice * $orderTax);
    }
}
