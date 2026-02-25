<?php

namespace App\Enums;

enum Department: string
{
    case CSE = 'CSE';
    case EEE = 'EEE';
    case ECE = 'ECE';
    case AI = 'AI';
    case AIDS = 'AIDS';

    public function label(): string
    {
        return match ($this) {
            self::CSE => 'Computer Science & Engineering',
            self::EEE => 'Electrical & Electronics Engineering',
            self::ECE => 'Electronics & Communication Engineering',
            self::AI => 'Artificial Intelligence',
            self::AIDS => 'AI & Data Science',
        };
    }
}
