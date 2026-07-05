<?php

namespace App\Enums;

enum AttemptStatus: string
{
    case STARTED = 'started';
    case SUBMITTED = 'submitted';
    case GRADED = 'graded';
    case EXPIRED = 'expired';
}
