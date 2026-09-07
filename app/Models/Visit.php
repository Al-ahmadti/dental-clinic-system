<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{
    protected $fillable = [
        'patient_id',
        'user_id',
        'visit_at',
        'status',
        'diagnosis',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'visit_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(VisitLineItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function totalLineAmount(): float
    {
        $items = $this->relationLoaded('lineItems')
            ? $this->lineItems
            : $this->lineItems()->get();

        return (float) ($items->sum(fn (VisitLineItem $line) => $line->lineTotal()) ?? 0);
    }

    public function totalPayments(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->sum('amount');
        }

        return (float) $this->payments()->sum('amount');
    }

    public function balanceDue(): float
    {
        return max(0, $this->totalLineAmount() - $this->totalPayments());
    }
}
