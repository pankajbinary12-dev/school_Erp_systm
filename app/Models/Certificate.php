<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'type',
        'certificate_no',
        'issue_date',
        'content',
        'qr_code',
        'issued_by',
        'status',
        'remarks'
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(Admin::class, 'issued_by');
    }

    // Certificate Types
    public static function types()
    {
        return [
            'bonafide' => 'Bonafide Certificate',
            'transfer' => 'Transfer Certificate (TC)',
            'character' => 'Character Certificate',
            'fee' => 'Fee Certificate',
            'migration' => 'Migration Certificate'
        ];
    }

    // Generate Certificate Number
    public static function generateCertificateNumber($type)
    {
        $year = date('Y');
        $typeCode = strtoupper(substr($type, 0, 3));
        
        $lastCert = self::whereYear('created_at', $year)
            ->where('type', $type)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastCert ? (int)substr($lastCert->certificate_no, -4) + 1 : 1;
        
        return "SCH/{$year}/{$typeCode}/" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Get Type Label
    public function getTypeLabel()
    {
        return self::types()[$this->type] ?? $this->type;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
