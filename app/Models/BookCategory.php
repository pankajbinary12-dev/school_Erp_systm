<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_name',
        'description',
        'status'
    ];

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }
}
