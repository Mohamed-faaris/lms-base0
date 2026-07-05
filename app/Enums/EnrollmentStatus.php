<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case ENROLLED = 'enrolled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case DROPPED = 'dropped';
}
