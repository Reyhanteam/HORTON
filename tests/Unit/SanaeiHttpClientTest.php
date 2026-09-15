<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTOs\SanaeiApiResponse;
use App\DTOs\Sanaei\SanaeiRequest;
use App\Exceptions\SanaeiApiException;
use App\Services\Providers\Sanaei\SanaeiHttpClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class SanaeiHttpClientTest extends TestCase
{
    public function test_server_and_inbound_read_write_methods_use_expected_endpoints(): void
    {
        Http::fake(fn ($request) => Http::response(['success' => true, 'obj' => ['ok' => true]], 200));

        $client = $this->client();
        $client->serverStatus();
        $client->openApi();
        $client->listInbounds();
        $client->getInbound(7);
        $client->addInbound(['remark' => 'de-01']);
        $client->updateInbound(7, ['remark' => 'de-02']);
        $client->deleteInbound(7);

        $urls = Http::recorded()->map(fn ($pair) => $pair[0]->url())->all();
        self::assertSame([
            'https://sanaei.test/panel/api/server/status',
            'https://sanaei.test/panel/api/server/openapi.json',
            'https://sanaei.test/panel/api/inbounds/list',
            'https://sanaei.test/panel/api/inbounds/get/7',
            'https://sanaei.test/panel/api/inbounds/add',
            'https://sanaei.test/panel/api/inbounds/update/7',
            'https://sanaei.test/panel/api/inbounds/del/7',
        ], $urls);
    }

    public function test_client_read_write_methods_use_expected_endpoints_and_bearer_auth(): void
    {
        Http::fake(fn ($request) => Http::response(['success' => true, 'obj' => ['ok' => true]], 200));

        $client = $this->client();
        $client->listClients();
        $client->getClient('alice@example.test');
        $client->addClient(['client' => ['email' => 'alice@example.test']]);
        $client->updateClient('alice@example.test', ['enable' => false]);
        $client->deleteClient('alice@example.test');
        $client->resetClientTraffic('alice@example.test');
        $client->attachClient('alice@example.test', ['inboundIds' => [1]]);
        $client->detachClient('alice@example.test', ['inboundIds' => [1]]);
        $client->subscriptionLinks('sub-123');

        $urls = Http::recorded()->map(fn ($pair) => $pair[0]->url())->all();
        self::assertSame([
            'https://sanaei.test/panel/api/clients/list',
            'https://sanaei.test/panel/api/clients/get/alice%40example.test',
            'https://sanaei.test/panel/api/clients/add',
            'https://sanaei.test/panel/api/clients/update/alice%40example.test',
            'https://sanaei.test/panel/api/clients/del/alice%40example.test',
            'https://sanaei.test/panel/api/clients/alice%40example.test/resetTraffic',
            'https://sanaei.test/panel/api/clients/alice%40example.test/attach',
            'https://sanaei.test/panel/api/clients/alice%40example.test/detach',
            'https://sanaei.test/panel/api/clients/subLinks/sub-123',
        ], $urls);

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer secret-token'));
    }

    public function test_failed_api_payload_becomes_standard_exception(): void
    {
        Http::fake(['https://sanaei.test/panel/api/clients/get/*' => Http::response([
            'success' => false,
            'msg' => 'client not found',
            'obj' => null,
        ], 200)]);

        $this->expectException(SanaeiApiException::class);
        $this->expectExceptionMessage('Read client failed: client not found');
        $this->client()->getClient('missing')->requireSuccess('Read client');
    }

    public function test_rate_limit_is_reported_as_retryable_and_secret_is_not_in_exception(): void
    {
        Http::fake(['https://sanaei.test/panel/api/server/status' => Http::response([
            'success' => false,
            'msg' => 'too many requests',
        ], 429)]);

        try {
            $this->client()->serverStatus()->requireSuccess('Read server status');
            self::fail('Expected SanaeiApiException.');
        } catch (SanaeiApiException $e) {
            self::assertTrue($e->retryable);
            self::assertStringNotContainsString('secret-token', $e->getMessage());
        }
    }

    public function test_generic_normalized_request_supports_read_and_write_without_exposing_credentials(): void
    {
        Http::fake(fn ($request) => Http::response(['success' => true, 'obj' => ['ok' => true]], 200));
        $client = $this->client();

        $response = $client->request(SanaeiRequest::get('/panel/api/server/status', 'Custom status'));
        self::assertInstanceOf(SanaeiApiResponse::class, $response);
        self::assertTrue($response->success);

        $client->request(SanaeiRequest::post('/panel/api/inbounds/update/7', ['remark' => 'x'], 'Custom update'));
        Http::assertSent(fn ($request) => $request->url() === 'https://sanaei.test/panel/api/inbounds/update/7'
            && $request['remark'] === 'x'
            && ! str_contains(json_encode($request->data(), JSON_THROW_ON_ERROR), 'secret-token'));
    }

    private function client(): SanaeiHttpClient
    {
        return SanaeiHttpClient::fromCredentials([
            'base_url' => 'https://sanaei.test/',
            'token' => 'secret-token',
            'timeout' => 5,
            'connect_timeout' => 2,
            'retry_times' => 0,
        ]);
    }
}
