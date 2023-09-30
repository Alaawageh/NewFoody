<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name' , 'name_ar' , 'total_quantity','threshold' ,'branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class,'product_ingredient')->withPivot('quantity','is_remove');
    }
    public function extraIngredient()
    {
        return $this->hasOne(ExtraIngredient::class);
    }
    public function orderProducts()
    {
        return $this->belongsToMany(OrderProduct::class, 'remove_ingredients')->withPivot('order_product_id','ingredient_id');
    }

}
