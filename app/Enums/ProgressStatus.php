<?php

namespace App\Enums;

enum ProgressStatus: string
{
    case NOT_STARTED = 'not_started';
    case STARTED = 'started';
    case COMPLETED = 'completed';
    case SKIPPED = 'skipped';
    case LOCKED = 'locked';
}
