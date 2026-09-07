<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PatientMedia extends Model
{
    public const KIND_BEFORE = 'before';

    public const KIND_AFTER = 'after';

    public const KIND_RADIOLOGY = 'radiology';

    protected $fillable = [
        'patient_id',
        'visit_id',
        'kind',
        'path',
        'original_name',
    ];

    protected static function booted(): void
    {
        static::deleting(function (PatientMedia $media): void {
            if (! $media->path) {
                return;
            }

            $disk = $media->storageDisk();
            if (Storage::disk($disk)->exists($media->path)) {
                Storage::disk($disk)->delete($media->path);
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public static function kindOptions(): array
    {
        return [
            self::KIND_BEFORE => 'قبل العلاج',
            self::KIND_AFTER => 'بعد العلاج',
            self::KIND_RADIOLOGY => 'أشعة',
        ];
    }

    public function kindLabel(): string
    {
        return self::kindOptions()[$this->kind] ?? (string) $this->kind;
    }

    /**
     * Tailwind-friendly badge color key: primary | secondary | slate
     */
    public function kindColor(): string
    {
        return match ($this->kind) {
            self::KIND_BEFORE => 'primary',
            self::KIND_AFTER => 'secondary',
            self::KIND_RADIOLOGY => 'slate',
            default => 'slate',
        };
    }

    public function isImage(): bool
    {
        $ext = strtolower(pathinfo($this->path ?? '', PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true);
    }

    public function storageDisk(): string
    {
        if ($this->path && Storage::disk('local')->exists($this->path)) {
            return 'local';
        }

        return 'public';
    }

    public function url(): string
    {
        return route('patients.media.show', [
            'patient' => $this->patient_id,
            'media' => $this->getKey(),
        ]);
    }
}
