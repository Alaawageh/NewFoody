<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemoveIngredient extends Model
{
    use HasFactory;

    protected $table = 'remove_ingredients';
    
    protected $fillable = ['order_product_id','product_ingredient_id'];

}
