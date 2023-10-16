<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destruction extends Model
{
    use HasFactory;
    protected $table = 'destructions';
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
