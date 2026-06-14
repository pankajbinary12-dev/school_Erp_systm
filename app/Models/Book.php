<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'book_no',
        'title',
        'category_id',
        'author',
        'publisher',
        'isbn',
        'publication_year',
        'quantity',
        'available_quantity',
        'price',
        'rack_no',
        'description',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'publication_year' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function issues()
    {
        return $this->hasMany(BookIssue::class, 'book_id');
    }
}
