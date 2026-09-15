<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Exceptions\SanaeiApiException;

final readonly class SanaeiApiResponse
{
    public function __construct(
        public bool $success,
        public ?string $message,
        public mixed $data,
        public int $status,
        public array $headers = [],
    ) {}

    public static function from(int $status, array $payload, array $headers = []): self
    {
        $success = (bool) ($payload['success'] ?? false);
        $message = isset($payload['msg']) ? (string) $payload['msg'] : null;

        return new self($success, $message, $payload['obj'] ?? null, $status, $headers);
    }

    public function requireSuccess(string $operation): mixed
    {
        if (! $this->success) {
            throw new SanaeiApiException(
                $operation.' failed'.($this->message ? ': '.$this->message : '.'),
                $this->status,
                $this->message,
            );
        }

        return $this->data;
    }
}
