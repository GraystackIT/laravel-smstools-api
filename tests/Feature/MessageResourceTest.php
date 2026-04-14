<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\SendMessageRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

function makeConnector(): SmstoolsConnector
{
    return new SmstoolsConnector(
        clientId: 'test-client-id',
        clientSecret: 'test-client-secret',
    );
}

it('is resolved from the container', function (): void {
    config([
        'smstools.client_id'     => 'test-client-id',
        'smstools.client_secret' => 'test-client-secret',
    ]);

    $this->app->instance(SmstoolsConnector::class, makeConnector());
    $this->app->forgetInstance(SmstoolsClient::class);

    expect(app(SmstoolsClient::class))->toBeInstanceOf(SmstoolsClient::class);
});

it('sends a message to a single recipient and returns a messageid', function (): void {
    $mockClient = new MockClient([
        SendMessageRequest::class => MockResponse::make(
            ['messageid' => 'h2md1ewkyzjkuyn9ak7pryw1evtyw3x'],
            200
        ),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->messages()->send(
        to: '436501234567',
        message: 'Hello World',
        sender: 'MyApp',
    );

    expect($result)
        ->toBeArray()
        ->toHaveKey('messageid', 'h2md1ewkyzjkuyn9ak7pryw1evtyw3x');

    $mockClient->assertSent(SendMessageRequest::class);
});

it('sends a message to multiple recipients and returns messageids', function (): void {
    $mockClient = new MockClient([
        SendMessageRequest::class => MockResponse::make(
            ['messageids' => ['h2md1ewkyzjkuyn9ak7pryw1evtyw3x', '678rjqhrjwg3r7t78te1yxfda9u3yt6']],
            200
        ),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->messages()->send(
        to: ['436501234567', '436501234568'],
        message: 'Hello everyone',
        sender: 'MyApp',
    );

    expect($result)
        ->toBeArray()
        ->toHaveKey('messageids')
        ->and($result['messageids'])->toHaveCount(2);
});

it('includes optional fields when provided', function (): void {
    $mockClient = new MockClient([
        SendMessageRequest::class => MockResponse::make(['messageid' => 'abc123'], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->messages()->send(
        to: '436501234567',
        message: 'Scheduled message',
        sender: 'MyApp',
        date: '2026-06-01 10:00',
        reference: 'order-999',
        test: true,
        subid: 42,
    );

    $mockClient->assertSent(function (SendMessageRequest $request): bool {
        $body = $request->body()->all();

        return $body['date'] === '2026-06-01 10:00'
            && $body['reference'] === 'order-999'
            && $body['test'] === true
            && $body['subid'] === 42;
    });
});

it('omits optional fields when not provided', function (): void {
    $mockClient = new MockClient([
        SendMessageRequest::class => MockResponse::make(['messageid' => 'abc123'], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->messages()->send(
        to: '436501234567',
        message: 'Hello',
        sender: 'MyApp',
    );

    $mockClient->assertSent(function (SendMessageRequest $request): bool {
        $body = $request->body()->all();

        return ! array_key_exists('date', $body)
            && ! array_key_exists('reference', $body)
            && ! array_key_exists('test', $body)
            && ! array_key_exists('subid', $body);
    });
});

it('throws SmstoolsException with error code 104 for invalid credentials', function (): void {
    $mockClient = new MockClient([
        SendMessageRequest::class => MockResponse::make(
            ['error' => '104', 'errorMsg' => 'Invalid credentials'],
            400
        ),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->messages()->send(
        to: '436501234567',
        message: 'Hello',
        sender: 'MyApp',
    ))->toThrow(SmstoolsException::class, 'Invalid credentials.');
});

it('throws SmstoolsException with error code 108 for insufficient credits', function (): void {
    $mockClient = new MockClient([
        SendMessageRequest::class => MockResponse::make(['error' => '108'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->messages()->send(
        to: '436501234567',
        message: 'Hello',
        sender: 'MyApp',
    ))->toThrow(SmstoolsException::class, 'Not enough credits.');
});

it('throws SmstoolsException with a plain string error from API', function (): void {
    $mockClient = new MockClient([
        SendMessageRequest::class => MockResponse::make(['error' => 'error parsing json'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->messages()->send(
        to: '436501234567',
        message: 'Hello',
        sender: 'MyApp',
    ))->toThrow(SmstoolsException::class, 'error parsing json');
});

it('exposes the HTTP status code and api error code on SmstoolsException', function (): void {
    $mockClient = new MockClient([
        SendMessageRequest::class => MockResponse::make(['error' => '104'], 401),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    try {
        (new SmstoolsClient($connector))->messages()->send(
            to: '436501234567',
            message: 'Hello',
            sender: 'MyApp',
        );
    } catch (SmstoolsException $e) {
        expect($e->getStatusCode())->toBe(401)
            ->and($e->getApiErrorCode())->toBe(104);
    }
});
