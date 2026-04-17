<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\AddBirthdayMessageRequest;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\GetBirthdayMessageRequest;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\ListBirthdayMessagesRequest;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\RemoveBirthdayMessageRequest;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\UpdateBirthdayMessageRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

// ─── add() ────────────────────────────────────────────────────────────────

it('adds a birthday message with required fields', function (): void {
    $mockClient = new MockClient([
        AddBirthdayMessageRequest::class => MockResponse::make(['id' => 7], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->birthdayMessages()->add(
        firstname: 'Jane',
        number:    '436501234567',
        birthday:  '06-15',
        message:   'Happy Birthday {firstname}!',
        sender:    'MyApp',
    );

    expect($result)->toBeArray()->toHaveKey('id', 7);

    $mockClient->assertSent(function (AddBirthdayMessageRequest $request): bool {
        $body = $request->body()->all();

        return $body['firstname'] === 'Jane'
            && $body['number'] === '436501234567'
            && $body['birthday'] === '06-15'
            && $body['message'] === 'Happy Birthday {firstname}!'
            && $body['sender'] === 'MyApp'
            && ! array_key_exists('lastname', $body)
            && ! array_key_exists('groupid', $body);
    });
});

it('adds a birthday message with all fields', function (): void {
    $mockClient = new MockClient([
        AddBirthdayMessageRequest::class => MockResponse::make(['id' => 8], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->birthdayMessages()->add(
        firstname: 'Jane',
        number:    '436501234567',
        birthday:  '06-15',
        message:   'Happy Birthday!',
        sender:    'MyApp',
        lastname:  'Doe',
        groupid:   3,
    );

    $mockClient->assertSent(function (AddBirthdayMessageRequest $request): bool {
        $body = $request->body()->all();

        return $body['lastname'] === 'Doe' && $body['groupid'] === 3;
    });
});

it('throws InvalidArgumentException for empty firstname in add', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->birthdayMessages()->add(
        firstname: '',
        number:    '436501234567',
        birthday:  '06-15',
        message:   'Happy Birthday!',
        sender:    'MyApp',
    ))->toThrow(\InvalidArgumentException::class, 'Firstname must not be empty.');
});

it('throws InvalidArgumentException for empty message in add', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->birthdayMessages()->add(
        firstname: 'Jane',
        number:    '436501234567',
        birthday:  '06-15',
        message:   '',
        sender:    'MyApp',
    ))->toThrow(\InvalidArgumentException::class, 'Message must not be empty.');
});

it('throws SmstoolsException on API error during add', function (): void {
    $mockClient = new MockClient([
        AddBirthdayMessageRequest::class => MockResponse::make(['error' => '139'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->birthdayMessages()->add(
        firstname: 'Jane',
        number:    '436501234567',
        birthday:  '06-15',
        message:   'Happy Birthday!',
        sender:    'MyApp',
    ))->toThrow(SmstoolsException::class);
});

// ─── list() ───────────────────────────────────────────────────────────────

it('lists all birthday messages', function (): void {
    $mockClient = new MockClient([
        ListBirthdayMessagesRequest::class => MockResponse::make([
            'birthdays' => [
                ['id' => 1, 'firstname' => 'Jane'],
                ['id' => 2, 'firstname' => 'Bob'],
            ],
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->birthdayMessages()->list();

    expect($result)->toBeArray()->toHaveKey('birthdays');
    expect($result['birthdays'])->toHaveCount(2);

    $mockClient->assertSent(ListBirthdayMessagesRequest::class);
});

// ─── get() ────────────────────────────────────────────────────────────────

it('retrieves a specific birthday message by ID', function (): void {
    $mockClient = new MockClient([
        GetBirthdayMessageRequest::class => MockResponse::make([
            'id'        => 1,
            'firstname' => 'Jane',
            'birthday'  => '06-15',
            'message'   => 'Happy Birthday!',
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->birthdayMessages()->get(1);

    expect($result)->toBeArray()
        ->toHaveKey('id', 1)
        ->toHaveKey('firstname', 'Jane');

    $mockClient->assertSent(GetBirthdayMessageRequest::class);
});

it('throws InvalidArgumentException when birthday message ID is zero for get', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->birthdayMessages()->get(0))
        ->toThrow(\InvalidArgumentException::class, 'Birthday message ID must be a positive integer.');
});

// ─── update() ─────────────────────────────────────────────────────────────

it('updates a birthday message with only the provided fields', function (): void {
    $mockClient = new MockClient([
        UpdateBirthdayMessageRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->birthdayMessages()->update(
        id:      1,
        message: 'Have a wonderful birthday!',
        sender:  'NewSender',
    );

    $mockClient->assertSent(function (UpdateBirthdayMessageRequest $request): bool {
        $body = $request->body()->all();

        return $body['id'] === 1
            && $body['message'] === 'Have a wonderful birthday!'
            && $body['sender'] === 'NewSender'
            && ! array_key_exists('firstname', $body)
            && ! array_key_exists('birthday', $body);
    });
});

it('throws InvalidArgumentException when birthday message ID is zero for update', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->birthdayMessages()->update(id: 0))
        ->toThrow(\InvalidArgumentException::class, 'Birthday message ID must be a positive integer.');
});

// ─── remove() ─────────────────────────────────────────────────────────────

it('removes a birthday message by ID', function (): void {
    $mockClient = new MockClient([
        RemoveBirthdayMessageRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->birthdayMessages()->remove(1);

    expect($result)->toBeArray()->toHaveKey('success', true);

    $mockClient->assertSent(function (RemoveBirthdayMessageRequest $request): bool {
        return str_ends_with($request->resolveEndpoint(), '/1');
    });
});

it('throws InvalidArgumentException when birthday message ID is zero for remove', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->birthdayMessages()->remove(0))
        ->toThrow(\InvalidArgumentException::class, 'Birthday message ID must be a positive integer.');
});
