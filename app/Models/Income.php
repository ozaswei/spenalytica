<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'incomes';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    //One category can many incomes
    public function category()
    {
        return $this->hasOne(Category::class, 'userId');
    }
}
