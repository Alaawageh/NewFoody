<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name' , 'password' , 'address', 'taxRate' ,'restaurant_id'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
    public function offer()
    {
        return $this->hasMany(Offer::class);
    }
    public function category()
    {
        return $this->hasMany(Category::class);
    }
    public function tables()
    {
        return $this->hasMany(Table::class);
    }
    public function repo()
    {
        return $this->hasOne(Repo::class);
    }
    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
