<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Enums\WebhookMethod;
use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Webhooks\CreateWebhookRequest;
use GraystackIT\SmstoolsApi\Requests\Webhooks\DeleteWebhookRequest;
use GraystackIT\SmstoolsApi\Requests\Webhooks\ListWebhooksRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

// ─── list() ───────────────────────────────────────────────────────────────

it('lists all registered webhooks', function (): void {
    $mockClient = new MockClient([
        ListWebhooksRequest::class => MockResponse::make([
            'webhooks' => [
                ['id' => 1, 'webhook_endpoint' => 'https://example.com/hook1', 'webhook_method' => 'POST', 'webhook_types' => ['sms_inbound']],
                ['id' => 2, 'webhook_endpoint' => 'https://example.com/hook2', 'webhook_method' => 'GET',  'webhook_types' => ['sms_delivered', 'sms_failed']],
            ],
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->webhooks()->list();

    expect($result)->toBeArray()->toHaveKey('webhooks');
    expect($result['webhooks'])->toHaveCount(2);

    $mockClient->assertSent(ListWebhooksRequest::class);
});

it('returns an empty list when no webhooks are registered', function (): void {
    $mockClient = new MockClient([
        ListWebhooksRequest::class => MockResponse::make(['webhooks' => []], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->webhooks()->list();

    expect($result['webhooks'])->toBeEmpty();
});

it('throws SmstoolsException on API error during list', function (): void {
    $mockClient = new MockClient([
        ListWebhooksRequest::class => MockResponse::make(['error' => '104'], 401),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->webhooks()->list())
        ->toThrow(SmstoolsException::class, 'Invalid credentials.');
});

// ─── create() ─────────────────────────────────────────────────────────────

it('creates a webhook and returns the secret token', function (): void {
    $mockClient = new MockClient([
        CreateWebhookRequest::class => MockResponse::make(
            ['secret' => 'ccc14fb1-bf8c-4ebf-882c-caccd4c95a2c'],
            200,
        ),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->webhooks()->create(
        webhookEndpoint: 'https://example.com/sms-hook',
        webhookMethod:   WebhookMethod::Post,
        webhookTypes:    ['sms_inbound', 'sms_delivered'],
    );

    expect($result)->toBeArray()->toHaveKey('secret', 'ccc14fb1-bf8c-4ebf-882c-caccd4c95a2c');
});

it('sends the correct body fields for create', function (): void {
    $mockClient = new MockClient([
        CreateWebhookRequest::class => MockResponse::make(['secret' => 'abc-secret'], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->webhooks()->create(
        webhookEndpoint: 'https://example.com/hook',
        webhookMethod:   WebhookMethod::Get,
        webhookTypes:    ['sms_failed'],
    );

    $mockClient->assertSent(function (CreateWebhookRequest $request): bool {
        $body = $request->body()->all();

        return $body['webhook_endpoint'] === 'https://example.com/hook'
            && $body['webhook_method'] === 'GET'
            && $body['webhook_types'] === ['sms_failed'];
    });
});

it('throws InvalidArgumentException for an invalid webhook endpoint URL', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->webhooks()->create(
        webhookEndpoint: 'not-a-valid-url',
        webhookMethod:   WebhookMethod::Post,
        webhookTypes:    ['sms_inbound'],
    ))->toThrow(\InvalidArgumentException::class, 'Invalid webhook endpoint URL: not-a-valid-url');
});

it('throws InvalidArgumentException when webhook_types is empty', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->webhooks()->create(
        webhookEndpoint: 'https://example.com/hook',
        webhookMethod:   WebhookMethod::Post,
        webhookTypes:    [],
    ))->toThrow(\InvalidArgumentException::class, 'webhook_types must contain at least one event type.');
});

it('throws SmstoolsException on API error during create', function (): void {
    $mockClient = new MockClient([
        CreateWebhookRequest::class => MockResponse::make(['error' => '139'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->webhooks()->create(
        webhookEndpoint: 'https://example.com/hook',
        webhookMethod:   WebhookMethod::Post,
        webhookTypes:    ['sms_inbound'],
    ))->toThrow(SmstoolsException::class, 'Missing or invalid parameters.');
});

// ─── delete() ─────────────────────────────────────────────────────────────

it('deletes a webhook by secret token', function (): void {
    $mockClient = new MockClient([
        DeleteWebhookRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->webhooks()->delete('ccc14fb1-bf8c-4ebf-882c-caccd4c95a2c');

    expect($result)->toBeArray()->toHaveKey('success', true);

    $mockClient->assertSent(function (DeleteWebhookRequest $request): bool {
        return str_ends_with($request->resolveEndpoint(), '/ccc14fb1-bf8c-4ebf-882c-caccd4c95a2c');
    });
});

it('throws InvalidArgumentException when webhook secret is empty', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->webhooks()->delete(''))
        ->toThrow(\InvalidArgumentException::class, 'Webhook secret must not be empty.');
});

it('throws SmstoolsException when webhook secret is not found', function (): void {
    $mockClient = new MockClient([
        DeleteWebhookRequest::class => MockResponse::make(['error' => '134'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->webhooks()->delete('invalid-secret'))
        ->toThrow(SmstoolsException::class, 'Webhook ID not found.');
});
