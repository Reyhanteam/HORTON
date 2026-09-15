<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\ServiceProviderOperation;

final readonly class ServiceProviderResult
{
    public function __construct(
        public bool $success,
        public ServiceProviderOperation $operation,
        public ?string $externalId = null,
        public ?string $externalReference = null,
        public array $data = [],
        public ?string $message = null,
    ) {}

    public static function success(ServiceProviderOperation $operation, array $data = [], ?string $externalId = null, ?string $externalReference = null): self
    {
        return new self(true, $operation, $externalId, $externalReference, $data);
    }
}
