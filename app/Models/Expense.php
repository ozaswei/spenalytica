<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    //One category can many expenses
    public function category()
    {
        return $this->belongsTo(Category::class,'categoryId','id');
    }
}
