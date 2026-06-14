<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'school_code',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
        'state',
        'pincode',
        'website',
        'logo',
        'favicon',
        'header_image',
        'principal_name',
        'principal_signature',
        'affiliation_no',
        'board',
        'about'
    ];
}
