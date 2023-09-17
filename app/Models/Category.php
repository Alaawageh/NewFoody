<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name' , 'name_ar' , 'image' , 'position' , 'branch_id','status'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function setImageAttribute ($image)
    {
        $newImageName = uniqid() . '_' . 'image' . '.' . $image->extension();
        $image->move(public_path('images/category') , $newImageName);
        return $this->attributes['image'] ='/'.'images/category'.'/' . $newImageName;
    }

    public function ReOrder($request)
    {
        $categories = Category::where('branch_id',$request->branch_id)->orderBy('position','ASC')->get();
        $i = 1;
        foreach($categories as $category){
            if($category->position !=null){
                $category->position = $i;
                $category->save();
                $i++;
            }
        }
        return $i;
    }
}
