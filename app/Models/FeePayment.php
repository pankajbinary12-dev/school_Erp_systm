<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_no',
        'student_id',
        'student_fee_id',
        'amount',
        'late_fee_paid',
        'payment_mode',
        'transaction_id',
        'cheque_no',
        'cheque_date',
        'bank_name',
        'remarks',
        'payment_date',
        'collected_by',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee_paid' => 'decimal:2',
        'payment_date' => 'date',
        'cheque_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function collectedBy()
    {
        return $this->belongsTo(Admin::class, 'collected_by');
    }

    public static function generateReceiptNo()
    {
        $year = date('Y');
        $lastReceipt = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastReceipt ? (int)substr($lastReceipt->receipt_no, -6) + 1 : 1;
        
        return "RCP/{$year}/" . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }
}
