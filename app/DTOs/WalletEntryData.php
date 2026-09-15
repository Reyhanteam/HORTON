<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class WalletEntryData
{
    public function __construct(
        public int $telegramAccountId,
        public int $amount,
        public string $type,
        public string $reference,
        public array $metadata = [],
    ) {}
}
