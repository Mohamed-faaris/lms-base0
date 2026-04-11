<?php

namespace App\Enums;

enum ContentType: string
{
    case Video = 'video';
    case Article = 'article';
    case PPT = 'ppt';
    case Quiz = 'quiz';

    public function label(): string
    {
        return match ($this) {
            self::Video => 'Video',
            self::Article => 'Article',
            self::PPT => 'Presentation',
            self::Quiz => 'Quiz',
        };
    }
}
