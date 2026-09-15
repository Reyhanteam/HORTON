<?php

declare(strict_types=1);

namespace App\Services\Providers\Sanaei;

use App\DTOs\SanaeiApiResponse;
use App\DTOs\Sanaei\SanaeiRequest;
use App\Exceptions\SanaeiApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        if ($baseUrl === '') throw new SanaeiApiException('Sanaei base URL is not configured.');
        if ($token === '') throw new SanaeiApiException('Sanaei API token is not configured.');
        self::validateBaseUrl($baseUrl);

        return new self(
            $baseUrl,
            $token,
            max(1, (int) ($credentials['timeout'] ?? 15)),
            max(1, (int) ($credentials['connect_timeout'] ?? 5)),
            max(0, (int) ($credentials['retry_times'] ?? 2)),
        );
    }

    public function get(string $path, string $operation): SanaeiApiResponse { return $this->send(SanaeiRequest::get($path, $operation)); }
    public function post(string $path, array $payload, string $operation): SanaeiApiResponse { return $this->send(SanaeiRequest::post($path, $payload, $operation)); }
    public function serverStatus(): SanaeiApiResponse { return $this->get('/panel/api/server/status', 'Read server status'); }
    public function openApi(): SanaeiApiResponse { return $this->get('/panel/api/server/openapi.json', 'Read server OpenAPI document'); }
    public function listInbounds(): SanaeiApiResponse { return $this->get('/panel/api/inbounds/list', 'List inbounds'); }
    public function getInbound(int|string $id): SanaeiApiResponse { return $this->get('/panel/api/inbounds/get/'.rawurlencode((string) $id), 'Read inbound'); }
    public function addInbound(array $payload): SanaeiApiResponse { return $this->post('/panel/api/inbounds/add', $payload, 'Create inbound'); }
    public function updateInbound(int|string $id, array $payload): SanaeiApiResponse { return $this->post('/panel/api/inbounds/update/'.rawurlencode((string) $id), $payload, 'Update inbound'); }
    public function deleteInbound(int|string $id): SanaeiApiResponse { return $this->post('/panel/api/inbounds/del/'.rawurlencode((string) $id), [], 'Delete inbound'); }
    public function listClients(): SanaeiApiResponse { return $this->get('/panel/api/clients/list', 'List clients'); }
    public function getClient(string $email): SanaeiApiResponse { return $this->get('/panel/api/clients/get/'.rawurlencode($email), 'Read client'); }
    public function addClient(array $payload): SanaeiApiResponse { return $this->post('/panel/api/clients/add', $payload, 'Create client'); }
    public function updateClient(string $email, array $payload): SanaeiApiResponse { return $this->post('/panel/api/clients/update/'.rawurlencode($email), $payload, 'Update client'); }
    public function deleteClient(string $email): SanaeiApiResponse { return $this->post('/panel/api/clients/del/'.rawurlencode($email), [], 'Delete client'); }
    public function resetClientTraffic(string $email): SanaeiApiResponse { return $this->post('/panel/api/clients/'.rawurlencode($email).'/resetTraffic', [], 'Reset client traffic'); }
    public function attachClient(string $email, array $payload = []): SanaeiApiResponse { return $this->post('/panel/api/clients/'.rawurlencode($email).'/attach', $payload, 'Attach client'); }
    public function detachClient(string $email, array $payload = []): SanaeiApiResponse { return $this->post('/panel/api/clients/'.rawurlencode($email).'/detach', $payload, 'Detach client'); }
    public function subscriptionLinks(string $subscriptionId): SanaeiApiResponse { return $this->get('/panel/api/clients/subLinks/'.rawurlencode($subscriptionId), 'Read subscription links'); }
    public function request(SanaeiRequest $request): SanaeiApiResponse { return $this->send($request); }

    private function send(SanaeiRequest $request): SanaeiApiResponse
    {
        $started = microtime(true);
        $attempts = $this->retryTimes + 1;
        $lastConnectionException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $pending = $this->requestBuilder();
                $response = $request->method === 'GET'
                    ? $pending->get($this->url($request->path))
                    : $pending->post($this->url($request->path), $request->payload);
            } catch (ConnectionException|RequestException $e) {
                $lastConnectionException = $e;
                if ($attempt < $attempts && $this->retryableException($e)) {
                    $this->backoff($attempt);
                    continue;
                }

                Log::warning('Sanaei API connection failure', [
                    'operation' => $request->operation,
                    'latency_ms' => $this->latency($started),
                    'exception' => $e::class,
                ]);
                $status = $e instanceof RequestException ? $e->response->status() : null;
                throw new SanaeiApiException($request->operation.'. connection failed.', $status, null, true, $e);
            }

            $body = $response->json();
            if (! is_array($body)) {
                Log::warning('Sanaei API returned an invalid response', [
                    'operation' => $request->operation,
                    'status' => $response->status(),
                    'latency_ms' => $this->latency($started),
                ]);
                throw new SanaeiApiException($request->operation.' returned an invalid response.', $response->status());
            }

            $apiResponse = SanaeiApiResponse::from($response->status(), $body, $response->headers());
            if ($this->retryableStatus($response->status()) && $attempt < $attempts) {
                Log::notice('Sanaei API retry scheduled', [
                    'operation' => $request->operation,
                    'status' => $response->status(),
                    'attempt' => $attempt,
                    'next_attempt' => $attempt + 1,
                ]);
                $this->backoff($attempt);
                continue;
            }

            if ($this->retryableStatus($response->status())) {
                Log::warning('Sanaei API returned a retryable error', [
                    'operation' => $request->operation,
                    'status' => $response->status(),
                    'latency_ms' => $this->latency($started),
                ]);
                throw new SanaeiApiException($request->operation.' failed with a retryable HTTP error.', $response->status(), $apiResponse->message, true);
            }

            if ($response->failed()) {
                Log::warning('Sanaei API returned an HTTP error', [
                    'operation' => $request->operation,
                    'status' => $response->status(),
                    'latency_ms' => $this->latency($started),
                ]);
            }

            return $apiResponse;
        }

        throw new SanaeiApiException($request->operation.'. connection failed.', null, null, true, $lastConnectionException);
    }

    private function requestBuilder(): PendingRequest
    {
        return Http::acceptJson()->asJson()->withToken($this->token)->timeout($this->timeout)->connectTimeout($this->connectTimeout);
    }

    private function retryableException(\Throwable $exception): bool
    {
        return $exception instanceof ConnectionException
            || ($exception instanceof RequestException && $this->retryableStatus($exception->response->status()));
    }

    private function retryableStatus(?int $status): bool
    {
        return in_array($status, [408, 425, 429, 500, 502, 503, 504], true);
    }

    private function backoff(int $attempt): void
    {
        usleep(min(2_000_000, 250_000 * (2 ** max(0, $attempt - 1))));
    }

    private function url(string $path): string { return $this->baseUrl.'/'.ltrim($path, '/'); }

    private static function validateBaseUrl(string $baseUrl): void
    {
        $parts = parse_url($baseUrl);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (! in_array($scheme, ['http', 'https'], true) || $host === '') throw new SanaeiApiException('Sanaei base URL must be a valid HTTP(S) URL.');
        if ((bool) config('horton.providers.sanaei.allow_private_urls', false)) return;
        if (self::isBlockedHost($host)) throw new SanaeiApiException('Sanaei base URL points to a blocked private or local destination.');
    }

    private static function isBlockedHost(string $host): bool
    {
        if (in_array($host, ['localhost', 'localhost.localdomain', 'metadata.google.internal'], true)) return true;
        if (! filter_var($host, FILTER_VALIDATE_IP)) return false;
        return ! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    private function latency(float $started): int { return (int) round((microtime(true) - $started) * 1000); }
}
