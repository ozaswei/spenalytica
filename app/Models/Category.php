<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    // protected $fillable = [];
    protected $guarded = [];

    //many Categories may belongs to one user, userId is the Foreign Key
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    //one category can have many expenses
    public function expenses(){
        return $this->hasMany(Expense::class,'categoryId');
    }

    //one category has many incomes
    public function incomes(){
        return $this->hasMany(Income::class,'categoryId');
    }
}
