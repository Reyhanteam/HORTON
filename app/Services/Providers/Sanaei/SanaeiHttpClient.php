<?php

declare(strict_types=1);

namespace App\Services\Providers\Sanaei;

use App\DTOs\SanaeiApiResponse;
use App\Exceptions\SanaeiApiException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class SanaeiHttpClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $token,
        private readonly int $timeout = 15,
        private readonly int $connectTimeout = 5,
        private readonly int $retryTimes = 2,
    ) {}

    public static function fromCredentials(array $credentials): self
    {
        $baseUrl = rtrim((string) ($credentials['base_url'] ?? ''), '/');
        $token = (string) ($credentials['token'] ?? '');

        if ($baseUrl === '') {
            throw new SanaeiApiException('Sanaei base URL is not configured.');
        }

        if ($token === '') {
            throw new SanaeiApiException('Sanaei API token is not configured.');
        }

        return new self(
            $baseUrl,
            $token,
            max(1, (int) ($credentials['timeout'] ?? 15)),
            max(1, (int) ($credentials['connect_timeout'] ?? 5)),
            max(0, (int) ($credentials['retry_times'] ?? 2)),
        );
    }

    public function get(string $path, string $operation): SanaeiApiResponse
    {
        return $this->send('GET', $path, null, $operation);
    }

    public function post(string $path, array $payload, string $operation): SanaeiApiResponse
    {
        return $this->send('POST', $path, $payload, $operation);
    }

    private function send(string $method, string $path, ?array $payload, string $operation): SanaeiApiResponse
    {
        try {
            $request = $this->request();
            $response = $method === 'GET'
                ? $request->get($this->url($path))
                : $request->post($this->url($path), $payload ?? []);
        } catch (\Throwable $e) {
            throw new SanaeiApiException($operation.' connection failed.', null, null, true, $e);
        }

        $body = $response->json();
        if (! is_array($body)) {
            throw new SanaeiApiException($operation.' returned an invalid response.', $response->status());
        }

        $apiResponse = SanaeiApiResponse::from($response->status(), $body, $response->headers());

        if ($response->status() === 429 || $response->serverError()) {
            throw new SanaeiApiException(
                $operation.' failed with a retryable HTTP error.',
                $response->status(),
                $apiResponse->message,
                true,
            );
        }

        return $apiResponse;
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->withToken($this->token)
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->retry($this->retryTimes, 250, function ($exception): bool {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException
                    || ($exception instanceof \Illuminate\Http\Client\RequestException
                        && in_array($exception->response?->status(), [408, 425, 429, 500, 502, 503, 504], true));
            }, throw: false);
    }

    private function url(string $path): string
    {
        return $this->baseUrl.'/'.ltrim($path, '/');
    }
}
