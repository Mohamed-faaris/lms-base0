<?php

namespace App\Enums;

enum ScopeType: string
{
    case GLOBAL = 'global';
    case ORGANIZATION = 'organization';
    case DEPARTMENT = 'department';
}
