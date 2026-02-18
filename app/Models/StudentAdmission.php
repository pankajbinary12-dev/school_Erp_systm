<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAdmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Student Basic Information
        'admission_no',
        'student_name',
        'dob',
        'gender',
        'class_id',
        'section_id',
        'student_email',
        'blood_group',
        'religion',
        'caste',
        'nationality',
        
        // Address Information
        'stu_address',
        'permanent_address',
        'city_name',
        'state_name',
        'pin_code',
        'phone',
        
        // Admission Information
        'admission_date',
        'previous_school',
        'previous_school_name',
        'previous_class',
        'tc_number',
        'status',
        
        // Father Information
        'father_name',
        'father_occupation',
        'father_phone',
        'father_email',
        'father_photo',
        
        // Mother Information
        'mother_name',
        'mother_phone',
        'mother_occupation',
        'mother_email',
        'mother_photo',
        
        // Guardian Information
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'relation',
        
        // Emergency Contact
        'emergency_contact',
        'contact_phone',
        
        // Documents
        'student_photo',
        'birth_certificate',
        'aadhar_card_front',
        'aadhar_card_back',
        
        // Medical Information
        'medical_info',
        'allergies',
    ];

    protected $casts = [
        'dob' => 'date',
        'admission_date' => 'date',
        'status' => 'boolean',
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    // Accessors
    public function getAgeAttribute()
    {
        return $this->dob ? $this->dob->age : null;
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->stu_address,
            $this->city_name,
            $this->state_name,
            $this->pin_code
        ]);
        return implode(', ', $parts);
    }
}
