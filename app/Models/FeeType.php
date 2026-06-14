<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'frequency',
        'is_mandatory',
        'status'
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
