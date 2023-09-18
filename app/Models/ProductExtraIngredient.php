<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExtraIngredient extends Model
{
    use HasFactory;
    protected $table = 'product_extra_ingredient';
    protected $fillable = [
        'product_id' ,'extra_ingredient_id' ,'quantity','price_per_piece'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function extraIngredient()
    {
        return $this->belongsTo(ExtraIngredient::class);
    }
}
