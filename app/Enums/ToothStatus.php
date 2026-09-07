<?php

namespace App\Enums;

enum ToothStatus: string
{
    case Healthy = 'healthy';
    case Decayed = 'decayed';
    case Treated = 'treated';
    case Missing = 'missing';

    public function label(): string
    {
        return match ($this) {
            self::Healthy => 'سليم',
            self::Decayed => 'تسوس',
            self::Treated => 'معالج',
            self::Missing => 'مفقود',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::Healthy => 'fill-emerald-400 stroke-emerald-700',
            self::Decayed => 'fill-red-400 stroke-red-800',
            self::Treated => 'fill-amber-300 stroke-amber-800',
            self::Missing => 'fill-slate-300 stroke-slate-600',
        };
    }
}
