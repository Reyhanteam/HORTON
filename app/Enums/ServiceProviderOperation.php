<?php

declare(strict_types=1);

namespace App\Enums;

enum ServiceProviderOperation: string
{
    case CREATE = 'create';
    case GET = 'get';
    case RENEW = 'renew';
    case EXTEND = 'extend';
    case ADD_CAPACITY = 'add_capacity';
    case DISABLE = 'disable';
    case DELETE = 'delete';
    case STATUS = 'status';
}
