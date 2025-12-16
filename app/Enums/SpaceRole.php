<?php

namespace App\Enums;

enum SpaceRole: string
{
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case COMMENTER = 'commenter';
    case VIEWER = 'viewer';
}
