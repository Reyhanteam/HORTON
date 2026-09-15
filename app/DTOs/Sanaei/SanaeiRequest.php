<?php

declare(strict_types=1);

namespace App\DTOs\Sanaei;

final readonly class SanaeiRequest
{
    public function __construct(
        public string $method,
        public string $path,
        public array $payload = [],
        public string $operation = 'Sanaei API request',
    ) {}

    public static function get(string $path, string $operation): self
    {
        return new self('GET', $path, [], $operation);
    }

    public static function post(string $path, array $payload, string $operation): self
    {
        return new self('POST', $path, $payload, $operation);
    }
}
