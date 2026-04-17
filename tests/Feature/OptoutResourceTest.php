<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Optouts\AddOptoutRequest;
use GraystackIT\SmstoolsApi\Requests\Optouts\ListOptoutsRequest;
use GraystackIT\SmstoolsApi\Requests\Optouts\RemoveOptoutRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

// ─── list() ───────────────────────────────────────────────────────────────

it('lists all opt-out numbers', function (): void {
    $mockClient = new MockClient([
        ListOptoutsRequest::class => MockResponse::make([
            'optouts' => [
                ['number' => '436501111111'],
                ['number' => '436502222222'],
            ],
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->optouts()->list();

    expect($result)->toBeArray()->toHaveKey('optouts');
    expect($result['optouts'])->toHaveCount(2);

    $mockClient->assertSent(ListOptoutsRequest::class);
});

it('lists opt-outs with custom pagination', function (): void {
    $mockClient = new MockClient([
        ListOptoutsRequest::class => MockResponse::make(['optouts' => []], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->optouts()->list(limit: 50, page: 2);

    $mockClient->assertSent(ListOptoutsRequest::class);
});

it('throws InvalidArgumentException for invalid limit in list', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->optouts()->list(limit: 0))
        ->toThrow(\InvalidArgumentException::class, 'Limit must be between 1 and 2000.');
});

it('throws InvalidArgumentException for invalid page in list', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->optouts()->list(page: 0))
        ->toThrow(\InvalidArgumentException::class, 'Page must be at least 1.');
});

it('throws SmstoolsException on API error during list', function (): void {
    $mockClient = new MockClient([
        ListOptoutsRequest::class => MockResponse::make(['error' => '104'], 401),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->optouts()->list())
        ->toThrow(SmstoolsException::class);
});

// ─── add() ────────────────────────────────────────────────────────────────

it('adds a number to the opt-out list', function (): void {
    $mockClient = new MockClient([
        AddOptoutRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->optouts()->add('436501234567');

    expect($result)->toBeArray()->toHaveKey('success', true);

    $mockClient->assertSent(function (AddOptoutRequest $request): bool {
        return $request->body()->all()['number'] === '436501234567';
    });
});

it('throws InvalidArgumentException when opt-out number is empty for add', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->optouts()->add(''))
        ->toThrow(\InvalidArgumentException::class, 'Opt-out number must not be empty.');
});

it('throws SmstoolsException with error 131 if number is already opted out', function (): void {
    $mockClient = new MockClient([
        AddOptoutRequest::class => MockResponse::make(['error' => '131'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->optouts()->add('436501234567'))
        ->toThrow(SmstoolsException::class, 'This number is on the opt-out list.');
});

// ─── remove() ─────────────────────────────────────────────────────────────

it('removes a number from the opt-out list', function (): void {
    $mockClient = new MockClient([
        RemoveOptoutRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->optouts()->remove('436501234567');

    expect($result)->toBeArray()->toHaveKey('success', true);

    $mockClient->assertSent(function (RemoveOptoutRequest $request): bool {
        return str_ends_with($request->resolveEndpoint(), '/436501234567');
    });
});

it('throws InvalidArgumentException when opt-out number is empty for remove', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->optouts()->remove(''))
        ->toThrow(\InvalidArgumentException::class, 'Opt-out number must not be empty.');
});

it('throws SmstoolsException on API error during remove', function (): void {
    $mockClient = new MockClient([
        RemoveOptoutRequest::class => MockResponse::make(['error' => '139'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->optouts()->remove('436501234567'))
        ->toThrow(SmstoolsException::class, 'Missing or invalid parameters.');
});
