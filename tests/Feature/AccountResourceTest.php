<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Account\GetAccountRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetBalanceRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetHistoryRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetInboxMessageRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetInboxRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetStatisticsRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

// ─── details() ────────────────────────────────────────────────────────────

it('retrieves account details', function (): void {
    $mockClient = new MockClient([
        GetAccountRequest::class => MockResponse::make([
            'name'  => 'Test Account',
            'email' => 'test@example.com',
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->account()->details();

    expect($result)->toBeArray()
        ->toHaveKey('name', 'Test Account')
        ->toHaveKey('email', 'test@example.com');

    $mockClient->assertSent(GetAccountRequest::class);
});

it('throws SmstoolsException on API error for account details', function (): void {
    $mockClient = new MockClient([
        GetAccountRequest::class => MockResponse::make(['error' => '104'], 401),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->account()->details())
        ->toThrow(SmstoolsException::class, 'Invalid credentials.');
});

// ─── balance() ────────────────────────────────────────────────────────────

it('retrieves the account balance', function (): void {
    $mockClient = new MockClient([
        GetBalanceRequest::class => MockResponse::make(['balance' => '42.50', 'currency' => 'EUR'], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->account()->balance();

    expect($result)->toBeArray()
        ->toHaveKey('balance', '42.50')
        ->toHaveKey('currency', 'EUR');

    $mockClient->assertSent(GetBalanceRequest::class);
});

// ─── history() ────────────────────────────────────────────────────────────

it('retrieves account history without filters', function (): void {
    $mockClient = new MockClient([
        GetHistoryRequest::class => MockResponse::make(['transactions' => []], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->account()->history();

    expect($result)->toBeArray()->toHaveKey('transactions');

    $mockClient->assertSent(GetHistoryRequest::class);
});

it('retrieves account history with date range', function (): void {
    $mockClient = new MockClient([
        GetHistoryRequest::class => MockResponse::make(['transactions' => [['amount' => '-0.05']]], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->account()->history(
        from:  '2024-01-01',
        to:    '2024-01-31',
        limit: 50,
    );

    $mockClient->assertSent(GetHistoryRequest::class);
});

it('throws InvalidArgumentException for invalid limit in history', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->account()->history(limit: 0))
        ->toThrow(\InvalidArgumentException::class, 'Limit must be between 1 and 2000.');
});

// ─── inbox() ──────────────────────────────────────────────────────────────

it('retrieves the inbox', function (): void {
    $mockClient = new MockClient([
        GetInboxRequest::class => MockResponse::make([
            'messages' => [
                ['id' => 'msg-001', 'from' => '436501234567', 'message' => 'Hello'],
            ],
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->account()->inbox();

    expect($result)->toBeArray()->toHaveKey('messages');
    expect($result['messages'])->toHaveCount(1);

    $mockClient->assertSent(GetInboxRequest::class);
});

it('retrieves the inbox filtered by type', function (): void {
    $mockClient = new MockClient([
        GetInboxRequest::class => MockResponse::make(['messages' => []], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->account()->inbox(type: 'sms');

    $mockClient->assertSent(function (GetInboxRequest $request): bool {
        return $request->query()->get('type') === 'sms';
    });
});

it('throws InvalidArgumentException for invalid page in inbox', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->account()->inbox(page: 0))
        ->toThrow(\InvalidArgumentException::class, 'Page must be at least 1.');
});

// ─── inboxMessage() ───────────────────────────────────────────────────────

it('retrieves a specific inbox message by ID', function (): void {
    $mockClient = new MockClient([
        GetInboxMessageRequest::class => MockResponse::make([
            'id'      => 'msg-001',
            'from'    => '436501234567',
            'message' => 'Hello reply',
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->account()->inboxMessage('msg-001');

    expect($result)->toBeArray()
        ->toHaveKey('id', 'msg-001')
        ->toHaveKey('message', 'Hello reply');

    $mockClient->assertSent(GetInboxMessageRequest::class);
});

it('throws InvalidArgumentException when inbox message ID is empty', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->account()->inboxMessage(''))
        ->toThrow(\InvalidArgumentException::class, 'Inbox message ID must not be empty.');
});

// ─── statistics() ─────────────────────────────────────────────────────────

it('retrieves account statistics without filters', function (): void {
    $mockClient = new MockClient([
        GetStatisticsRequest::class => MockResponse::make([
            'sent'      => 150,
            'delivered' => 140,
            'failed'    => 10,
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->account()->statistics();

    expect($result)->toBeArray()
        ->toHaveKey('sent', 150)
        ->toHaveKey('delivered', 140);

    $mockClient->assertSent(GetStatisticsRequest::class);
});

it('retrieves statistics for a specific year and month', function (): void {
    $mockClient = new MockClient([
        GetStatisticsRequest::class => MockResponse::make(['sent' => 30], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->account()->statistics(
        year:  '2024',
        month: '01',
    );

    $mockClient->assertSent(function (GetStatisticsRequest $request): bool {
        return $request->query()->get('year') === '2024'
            && $request->query()->get('month') === '01';
    });
});
