<?php

namespace App\Enums;

enum AdminUserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Blocked = 'blocked';
}
