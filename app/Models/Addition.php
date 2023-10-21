<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addition extends Model
{
    use HasFactory;
    protected $table = 'additions';
    protected $fillable = [
        'ingredient_id' , 'qty' , 'unit' ,'branch_id'
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
