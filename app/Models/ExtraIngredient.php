<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity' , 'price_per_piece' , 'repo_id'
    ];
    public function repo()
    {
        return $this->belongsTo(Repo::class);
    }
}
