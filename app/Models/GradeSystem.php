<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeSystem extends Model
{
    use HasFactory;

    protected $table = 'grade_systems';

    protected $fillable = [
        'grade',
        'min_percentage',
        'max_percentage',
        'grade_point',
        'description',
        'status'
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orderBy('min_percentage', 'desc');
    }
}
