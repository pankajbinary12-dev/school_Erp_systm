<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeDiscount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'description',
        'valid_from',
        'valid_to',
        'status'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function calculateDiscount($amount)
    {
        if ($this->type === 'percentage') {
            return ($amount * $this->value) / 100;
        }
        return min($this->value, $amount);
    }

    public function isValid()
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now();
        
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_to && $now->gt($this->valid_to)) {
            return false;
        }

        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
