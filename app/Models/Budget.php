<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'userId',
    //     'categoryId',
    //     'amount',
    //     'period',
    //     'start_date',
    //     'active',
    // ];
    protected $table = 'budgets';
    protected $guarded = [];
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'categoryId');
    }
}
