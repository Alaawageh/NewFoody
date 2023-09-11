<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repo extends Model
{
    use HasFactory;
    protected $fillable = [
        'name' , 'qty' ,'branch_id'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function extraIngredient()
    {
        return $this->hasOne(ExtraIngredient::class);
    }
}
