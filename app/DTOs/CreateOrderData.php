<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class CreateOrderData
{
    public function __construct(
        public int $telegramAccountId,
        public int $planId,
        public int $unitPrice,
        public int $quantity = 1,
        public int $discountAmount = 0,
        public ?string $discountCode = null,
        public array $metadata = [],
    ) {}

    public function subtotal(): int
    {
        return $this->unitPrice * $this->quantity;
    }

    public function total(): int
    {
        return max(0, $this->subtotal() - $this->discountAmount);
    }
}
