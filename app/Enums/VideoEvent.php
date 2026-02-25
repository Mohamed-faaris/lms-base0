<?php

namespace App\Enums;

enum VideoEvent: string
{
    case Pause = 'pause';
    case Stop = 'stop';
    case Start = 'start';

    public function label(): string
    {
        return match ($this) {
            self::Pause => 'Pause',
            self::Stop => 'Stop',
            self::Start => 'Start',
        };
    }
}
