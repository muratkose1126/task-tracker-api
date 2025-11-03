<?php

namespace App\Enums;

enum TaskCommentType: string
{
    case NOTE = 'note';
    case UPDATE = 'update';
    case REMINDER = 'reminder';
}
