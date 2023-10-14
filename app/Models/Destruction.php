<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destruction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id' , 'qty' , 'unit' ,'branch_id'
    ];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class);
    }
    public function branch()
    {
        return $this->belongsToMany(Branch::class);
    }
}
