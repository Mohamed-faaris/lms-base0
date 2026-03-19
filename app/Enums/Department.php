<?php

namespace App\Enums;

enum Department: string
{
    case CSE = 'CSE';
    case EEE = 'EEE';
    case ECE = 'ECE';
    case AI = 'AI';
    case AIDS = 'AIDS';
    case CIVIL = 'CIVIL';
    case MECH = 'MECH';
    case IT = 'IT';
    case SH = 'S&H';


    public function label(): string
    {
        return match ($this) {
            self::CSE => 'Computer Science & Engineering',
            self::EEE => 'Electrical & Electronics Engineering',
            self::ECE => 'Electronics & Communication Engineering',
            self::AI => 'Artificial Intelligence',
            self::AIDS => 'AI & Data Science',
            self::CIVIL => 'Civil Engineering',
            self::MECH => 'Mechanical Engineering',
            self::IT => 'Information Technology',
            self::SH => 'Science & Humanities',
        };
    }
}
