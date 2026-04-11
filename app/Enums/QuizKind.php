<?php

namespace App\Enums;

enum QuizKind: string
{
    case Content = 'content';
    case End = 'end';
    case Timestamped = 'timestamped';

    public function label(): string
    {
        return match ($this) {
            self::Content => 'Quiz Content',
            self::End => 'End Quiz',
            self::Timestamped => 'Timestamped Quiz',
        };
    }
}
