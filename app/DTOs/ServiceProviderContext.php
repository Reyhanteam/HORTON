<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\ServiceProvider;
use App\Models\ServiceProviderAccount;

final readonly class ServiceProviderContext
{
    public function __construct(
        public ?ServiceProvider $provider = null,
        public ?ServiceProviderAccount $account = null,
        public ?string $idempotencyKey = null,
        public array $inbounds = [],
        public array $attributes = [],
    ) {}

    public function with(array $attributes): self
    {
        return new self(
            $this->provider,
            $this->account,
            $this->idempotencyKey,
            $this->inbounds,
            [...$this->attributes, ...$attributes],
        );
    }
}
