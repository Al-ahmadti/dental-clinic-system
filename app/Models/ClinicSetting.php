<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $fillable = [
        'clinic_name',
        'address',
        'phone',
        'logo_path',
        'primary_color',
        'secondary_color',
        'accent_color',
    ];

    public static function current(): ?self
    {
        return static::query()->first();
    }
}
