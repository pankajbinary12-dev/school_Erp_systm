<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'enquiry_number',
        'enquiry_date',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'class_id',
        'session_id',
        'previous_school',
        'previous_class',
        'previous_percentage',
        'father_name',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_phone',
        'mother_occupation',
        'annual_income',
        'status',
        'remarks',
        'source',
        'reference_by',
        'follow_up_date',
        'follow_up_notes',
        'registration_fee',
        'fee_status',
        'fee_paid',
        'fee_paid_date',
        'payment_mode',
        'transaction_id',
        'student_id',
        'admission_number',
        'admission_date',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'enquiry_date' => 'date',
        'date_of_birth' => 'date',
        'follow_up_date' => 'date',
        'fee_paid_date' => 'date',
        'admission_date' => 'date',
        'approved_at' => 'datetime',
        'registration_fee' => 'decimal:2',
        'fee_paid' => 'decimal:2',
        'annual_income' => 'decimal:2',
        'previous_percentage' => 'decimal:2',
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getBalanceAmountAttribute()
    {
        return $this->registration_fee - $this->fee_paid;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'Converted');
    }

    public function scopeFeePending($query)
    {
        return $query->where('fee_status', 'Pending');
    }

    public function scopeFeePaid($query)
    {
        return $query->where('fee_status', 'Paid');
    }

    // Helper Methods
    public static function generateEnquiryNumber()
    {
        $year = date('Y');
        $lastEnquiry = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEnquiry) {
            $lastNumber = intval(substr($lastEnquiry->enquiry_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'ENQ/' . $year . '/' . $newNumber;
    }

    public function canConvertToAdmission()
    {
        return $this->status === 'Approved' && $this->fee_status === 'Paid';
    }
}
