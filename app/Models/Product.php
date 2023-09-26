<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name' , 'name_ar' , 'description' , 'description_ar' , 'price',
        'position' , 'image' , 'estimated_time' , 'status' , 'category_id' , 'branch_id'
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class,'product_ingredient')->withPivot('quantity','is_remove');
    }
    public function extraIngredients()
    {
        return $this->belongsToMany(ExtraIngredient::class,'product_extra_ingredient')->withPivot('quantity','price_per_piece');
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class,'order_products')->withPivot('qty','note','subTotal');
    }
    
    public function rating()
    {
        return $this->hasMany(Rating::class);
    }

    public function setImageAttribute ($image)
    {
        $newImageName = uniqid() . '_' . 'image' . '.' . $image->extension();
        $image->move(public_path('images/product') , $newImageName);
        return $this->attributes['image'] ='/'.'images/product'.'/' . $newImageName;
    }

    public function ReOrder($request)
    {
        $products = Product::where('category_id',$request->category_id)->orderBy('position','ASC')->get();
        $i = 1;
        foreach($products as $product){
            if($product->position !=null){
                $product->position = $i;
                $product->save();
                $i++;
            }
        }
        return $i;
    }

}
