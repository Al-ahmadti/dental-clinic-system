<?php

namespace App\Support;

final class FdiTooth
{
    /** @return list<int> */
    public static function permanentAdultTeeth(): array
    {
        return array_merge(
            range(11, 18),
            range(21, 28),
            range(31, 38),
            range(41, 48),
        );
    }

    public static function isValid(int $fdi): bool
    {
        return in_array($fdi, self::permanentAdultTeeth(), true);
    }
}
