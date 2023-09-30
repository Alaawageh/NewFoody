<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id' , 'product_id' , 'qty' , 'note' , 'subTotal'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function extra()
    {
        return $this->belongsToMany(ExtraIngredient::class,'order_product_extra_ingredient');              
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'remove_ingredients')->withPivot('order_product_id','ingredient_id');
    }

}
