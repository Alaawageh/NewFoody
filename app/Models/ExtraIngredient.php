<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraIngredient extends Model
{
    use HasFactory;

    protected $fillable = ['name' ,'name_ar','price_per_peice'];

    public function products()
    {
        return $this->belongsToMany(Product::class,'product_extra_ingredient')->withPivot('quantity');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class,'order_product_extra_ingredient');
    }
}