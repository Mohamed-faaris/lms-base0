<?php

namespace App\Enums;

enum QuestionType: string
{
    case MCQ = 'mcq';
    case MULTIPLE = 'multiple';
    case TRUE_FALSE = 'true_false';
    case FILL_BLANK = 'fill_blank';
    case SUBJECTIVE = 'subjective';
}
