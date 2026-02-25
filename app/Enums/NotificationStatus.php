<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case Active = 'active';
    case Viewed = 'viewed';
    case Deleted = 'deleted';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Viewed => 'Viewed',
            self::Deleted => 'Deleted',
        };
    }
}
