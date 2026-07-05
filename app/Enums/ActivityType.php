<?php

namespace App\Enums;

enum ActivityType: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case ASSIGN = 'assign';
    case COMPLETE = 'complete';
}
