<?php

namespace App\Models;

use App\Http\Middleware\Waiter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status' , 'total_price' , 'is_paid' , 'is_update' ,'time',
        'time_end' , 'time_Waiter' , 'table_id' , 'branch_id' , 'serviceRate' ,'feedback','author'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    public function product()
    {
        return $this->belongsToMany(Product::class,'order_products')->withPivot('qty','note','subTotal');
    }

    // public function extraIngredients()
    // {
    //     return $this->belongsToMany(ExtraIngredient::class,'order_product_extra_ingredient')->withPivot('order_id','product_id','extra_ingredient_id','total');
    // }
    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }


}
