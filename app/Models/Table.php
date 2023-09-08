<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_num','branch_id'
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
