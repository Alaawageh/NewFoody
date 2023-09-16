<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraIngredient extends Model
{
    use HasFactory;

    protected $fillable = ['name' ,'name_ar','price_per_kilo','branch_id'];

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class,'product_extra_ingredient')->withPivot('quantity');
    // }

    // public function orders()
    // {
    //     return $this->belongsToMany(Order::class,'order_product_extra_ingredient');
    // }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class,'product_extra_ingredient')
                    ->withPivot('quantity');
    }

    public function orderProducts()
    {
        return $this->belongsToMany(OrderProduct::class,'order_products')
                    ->withPivot('total');
    }
}
