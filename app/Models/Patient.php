<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'file_number',
        'name',
        'phone',
        'gender',
        'birth_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Patient $patient): void {
            if ($patient->file_number === '') {
                $patient->file_number = null;
            }
        });
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function patientTeeth(): HasMany
    {
        return $this->hasMany(PatientTooth::class);
    }

    public function toothTreatmentHistories(): HasMany
    {
        return $this->hasMany(ToothTreatmentHistory::class);
    }

    public function patientMedia(): HasMany
    {
        return $this->hasMany(PatientMedia::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PatientAuditLog::class)->orderByDesc('created_at');
    }
}
