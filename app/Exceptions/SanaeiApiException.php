<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class SanaeiApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $status = null,
        public readonly ?string $providerMessage = null,
        public readonly bool $retryable = false,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $status ?? 0, $previous);
    }
}
