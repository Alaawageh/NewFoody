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
        return $this->belongsToMany(Product::class,'product_ingredient')->withPivot('quantity');
    }
    public function extraIngredient()
    {
        return $this->hasOne(ExtraIngredient::class);
    }

}
