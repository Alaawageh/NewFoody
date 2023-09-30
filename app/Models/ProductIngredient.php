<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIngredient extends Model
{
    use HasFactory;
    protected $table = 'product_ingredient';
    protected $fillable = [
        'product_id' , 'ingredient_id' , 'quantity','is_remove'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
    // public function orderProducts()
    // {
    //     return $this->belongsToMany(OrderProduct::class,'order_products');              
    // }

    
}
