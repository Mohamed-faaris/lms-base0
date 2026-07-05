<?php

namespace App\Enums;

enum NotificationType: string
{
    case COURSE_ASSIGNED = 'course_assigned';
    case QUIZ_AVAILABLE = 'quiz_available';
    case DEADLINE = 'deadline';
    case CERTIFICATE = 'certificate';
    case SYSTEM = 'system';
}
