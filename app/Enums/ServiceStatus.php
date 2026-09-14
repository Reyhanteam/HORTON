<?php

namespace App\Enums;

enum ServiceStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';
    case Disabled = 'disabled';
    case Failed = 'failed';
}
