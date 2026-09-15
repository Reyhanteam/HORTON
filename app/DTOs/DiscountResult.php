<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class DiscountResult
{
    public function __construct(
        public ?int $discountCodeId,
        public int $amount,
        public int $total,
        public ?string $code = null,
    ) {}
}
