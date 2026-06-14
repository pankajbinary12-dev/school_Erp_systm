<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'total_amount',
        'discount_amount',
        'discount_reason',
        'paid_amount',
        'due_amount',
        'late_fee',
        'due_date',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function calculateLateFee()
    {
        if ($this->due_date && now()->gt($this->due_date)) {
            $daysLate = now()->diffInDays($this->due_date);
            $structure = $this->feeStructure;
            
            if ($daysLate >= $structure->late_fee_days) {
                return $structure->late_fee_amount;
            }
        }
        return 0;
    }

    public function updateStatus()
    {
        if ($this->paid_amount >= $this->total_amount) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } elseif ($this->due_date && now()->gt($this->due_date)) {
            $this->status = 'overdue';
        } else {
            $this->status = 'pending';
        }
        $this->save();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }
}
