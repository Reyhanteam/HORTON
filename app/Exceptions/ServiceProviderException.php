<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ServiceProviderOperation;
use RuntimeException;

final class ServiceProviderException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?ServiceProviderOperation $operation = null,
        public readonly bool $retryable = false,
        public readonly array $context = [],
    ) {
        parent::__construct($message);
    }
}
