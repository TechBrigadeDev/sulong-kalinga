<?php

namespace App\Enums;

enum LogType: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case ARCHIVE = 'archive';
    case DELETE = 'delete';
    case VIEW = 'view';
}