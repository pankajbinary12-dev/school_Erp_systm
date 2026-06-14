<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookIssue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'book_id',
        'member_type',
        'member_id',
        'issue_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'remarks'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function member()
    {
        if ($this->member_type === 'Student') {
            return $this->belongsTo(Student::class, 'member_id');
        } elseif ($this->member_type === 'Teacher') {
            return $this->belongsTo(Teacher::class, 'member_id');
        } elseif ($this->member_type === 'Staff') {
            return $this->belongsTo(StaffMember::class, 'member_id');
        }
        return null;
    }

    public function getMemberNameAttribute()
    {
        $member = null;
        if ($this->member_type === 'Student') {
            $member = Student::find($this->member_id);
        } elseif ($this->member_type === 'Teacher') {
            $member = Teacher::find($this->member_id);
        } elseif ($this->member_type === 'Staff') {
            $member = StaffMember::find($this->member_id);
        }
        
        if ($member) {
            return $member->first_name . ' ' . $member->last_name;
        }
        return 'N/A';
    }
}
