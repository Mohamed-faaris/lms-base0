<?php

namespace App\Enums;

enum QuizType: string
{
    case ANNOUNCED = 'announced';
    case SURPRISE = 'surprise';
    case PRACTICE = 'practice';
    case FINAL = 'final';
}
