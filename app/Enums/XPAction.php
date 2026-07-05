<?php

namespace App\Enums;

enum XPAction: string
{
    case COURSE_COMPLETED = 'course_completed';
    case MODULE_COMPLETED = 'module_completed';
    case QUIZ_PASSED = 'quiz_passed';
    case STREAK = 'streak';
    case BONUS = 'bonus';
}
