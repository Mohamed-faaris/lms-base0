<?php

namespace App\Enums;

enum College: string
{
    case KRCE = 'krce';
    case KRCT = 'krct';
    case MKCE = 'mkce';

    public function label(): string
    {
        return match ($this) {
            self::KRCE => 'KRCE',
            self::KRCT => 'KRCT',
            self::MKCE => 'MKCE',
        };
    }
}
