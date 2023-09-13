<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductExtraIngredient extends Model
{
    use HasFactory;
    protected $table = 'order_product_extra_ingredient';
    protected $fillable = [
        'order_id' ,'product_id', 'extra_ingredient_id' ,'total'
    ];

}
