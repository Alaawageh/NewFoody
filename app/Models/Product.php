<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name' , 'name_ar' , 'description' , 'description_ar' , 'price',
        'position' , 'image' , 'estimated_time' , 'status' ,
        'extraIng' ,'ingredient', 'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
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

    protected $casts = [
        'extraIng' => 'array',
        'ingredient' => 'array'
  
    ];
}
