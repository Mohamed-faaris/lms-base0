<?php

namespace App\Enums;

enum ModuleItemType: string
{
    case VIDEO = 'video';
    case PDF = 'pdf';
    case ARTICLE = 'article';
    case QUIZ = 'quiz';
    case ASSIGNMENT = 'assignment';
    case SURVEY = 'survey';
    case EXTERNAL_LINK = 'external_link';
    case CUSTOM_PAGE = 'custom_page';
}
