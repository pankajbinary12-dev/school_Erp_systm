<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeStructure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_id',
        'fee_type_id',
        'session_id',
        'amount',
        'due_date',
        'late_fee_amount',
        'late_fee_days',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
