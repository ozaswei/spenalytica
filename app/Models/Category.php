<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [];
    // protected $guarded = [];

    //many Categories may belongs to one user, userId is the Foreign Key
    public function user()
    {
        return $this->belongsTo(User::class,'userId');
    }
}
