<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class PaymentRequest
{
    public function __construct(
        public int $orderId,
        public int $amount,
        public string $currency = 'IRT',
        public array $metadata = [],
    ) {}
}

final readonly class PaymentResult
{
    public function __construct(
        public string $status,
        public ?string $authority = null,
        public ?string $reference = null,
        public array $metadata = [],
    ) {}
}
