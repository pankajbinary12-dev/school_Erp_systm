<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdCardTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name',
        'border_style',
        'border_color',
        'background_color',
        'text_color',
        'header_bg_color',
        'show_logo',
        'show_qr_code',
        'show_barcode',
        'is_active'
    ];

    protected $casts = [
        'show_logo' => 'boolean',
        'show_qr_code' => 'boolean',
        'show_barcode' => 'boolean'
    ];
}
