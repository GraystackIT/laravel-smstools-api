<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Contacts\AddContactRequest;
use GraystackIT\SmstoolsApi\Requests\Contacts\ListContactsRequest;
use GraystackIT\SmstoolsApi\Requests\Contacts\RemoveContactRequest;
use GraystackIT\SmstoolsApi\Requests\Contacts\SearchContactRequest;
use GraystackIT\SmstoolsApi\Requests\Contacts\UpdateContactRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

// ─── add() ────────────────────────────────────────────────────────────────

it('adds a contact with required fields only', function (): void {
    $mockClient = new MockClient([
        AddContactRequest::class => MockResponse::make(['id' => 42], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->contacts()->add(
        phone:   '436501234567',
        groupid: 1,
    );

    expect($result)->toBeArray()->toHaveKey('id', 42);

    $mockClient->assertSent(AddContactRequest::class);
});

it('adds a contact with all optional fields', function (): void {
    $mockClient = new MockClient([
        AddContactRequest::class => MockResponse::make(['id' => 99], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->contacts()->add(
        phone:        '436501234567',
        groupid:      5,
        firstname:    'Jane',
        lastname:     'Doe',
        birthday:     '1990-06-15',
        unsubscribed: false,
        extra:        ['extra1' => 'vip', 'extra2' => 'ref-123'],
    );

    $mockClient->assertSent(function (AddContactRequest $request): bool {
        $body = $request->body()->all();

        return $body['phone'] === '436501234567'
            && $body['groupid'] === 5
            && $body['firstname'] === 'Jane'
            && $body['lastname'] === 'Doe'
            && $body['birthday'] === '1990-06-15'
            && $body['unsubscribed'] === false
            && $body['extra1'] === 'vip'
            && $body['extra2'] === 'ref-123';
    });
});

it('throws InvalidArgumentException when phone is empty for add', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->contacts()->add(
        phone:   '',
        groupid: 1,
    ))->toThrow(\InvalidArgumentException::class, 'Contact phone must not be empty.');
});

it('throws InvalidArgumentException when groupid is invalid for add', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->contacts()->add(
        phone:   '436501234567',
        groupid: 0,
    ))->toThrow(\InvalidArgumentException::class, 'Contact groupid must be a positive integer.');
});

it('throws InvalidArgumentException for invalid extra field key', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->contacts()->add(
        phone:   '436501234567',
        groupid: 1,
        extra:   ['extra9' => 'invalid'],
    ))->toThrow(\InvalidArgumentException::class, "Invalid extra field key 'extra9'. Allowed: extra1–extra8.");
});

it('throws SmstoolsException on API error during add', function (): void {
    $mockClient = new MockClient([
        AddContactRequest::class => MockResponse::make(['error' => '104'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->contacts()->add(
        phone:   '436501234567',
        groupid: 1,
    ))->toThrow(SmstoolsException::class);
});

// ─── update() ─────────────────────────────────────────────────────────────

it('updates a contact with only the fields provided', function (): void {
    $mockClient = new MockClient([
        UpdateContactRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->contacts()->update(
        id:        42,
        firstname: 'John',
        phone:     '436509876543',
    );

    $mockClient->assertSent(function (UpdateContactRequest $request): bool {
        $body = $request->body()->all();

        return $body['id'] === 42
            && $body['firstname'] === 'John'
            && $body['phone'] === '436509876543'
            && ! array_key_exists('lastname', $body)
            && ! array_key_exists('groupid', $body);
    });
});

it('updates a contact birthday and extra fields', function (): void {
    $mockClient = new MockClient([
        UpdateContactRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->contacts()->update(
        id:           10,
        birthday:     '1985-03-22',
        unsubscribed: true,
        extra:        ['extra3' => 'premium'],
    );

    $mockClient->assertSent(function (UpdateContactRequest $request): bool {
        $body = $request->body()->all();

        return $body['id'] === 10
            && $body['birthday'] === '1985-03-22'
            && $body['unsubscribed'] === true
            && $body['extra3'] === 'premium';
    });
});

it('throws InvalidArgumentException when ID is zero for update', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->contacts()->update(id: 0))
        ->toThrow(\InvalidArgumentException::class, 'Contact ID must be a positive integer.');
});

// ─── search() ─────────────────────────────────────────────────────────────

it('searches contacts by query term', function (): void {
    $mockClient = new MockClient([
        SearchContactRequest::class => MockResponse::make([
            'contacts' => [['id' => 1, 'firstname' => 'Jane', 'phone' => '436501234567']],
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->contacts()->search(query: 'Jane');

    expect($result)->toBeArray()->toHaveKey('contacts');
});

it('throws InvalidArgumentException when search query is empty', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->contacts()->search(query: ''))
        ->toThrow(\InvalidArgumentException::class, 'Search query must not be empty.');
});

it('validates page and limit parameters for search', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->contacts()->search(
        query: 'Jane',
        limit: 9999,
    ))->toThrow(\InvalidArgumentException::class, 'Limit must be between 1 and 2000.');

    expect(fn () => (new SmstoolsClient($connector))->contacts()->search(
        query: 'Jane',
        page:  0,
    ))->toThrow(\InvalidArgumentException::class, 'Page must be at least 1.');
});

// ─── list() ───────────────────────────────────────────────────────────────

it('lists all contacts', function (): void {
    $mockClient = new MockClient([
        ListContactsRequest::class => MockResponse::make([
            'contacts' => [
                ['id' => 1, 'firstname' => 'Alice', 'phone' => '436501111111'],
                ['id' => 2, 'firstname' => 'Bob',   'phone' => '436502222222'],
            ],
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->contacts()->list();

    expect($result)->toBeArray()->toHaveKey('contacts');
    expect($result['contacts'])->toHaveCount(2);
});

it('lists contacts filtered by groupid', function (): void {
    $mockClient = new MockClient([
        ListContactsRequest::class => MockResponse::make(['contacts' => []], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->contacts()->list(groupid: 7);

    $mockClient->assertSent(SearchContactRequest::class === false
        ? ListContactsRequest::class
        : ListContactsRequest::class);
});

it('throws SmstoolsException on API error during list', function (): void {
    $mockClient = new MockClient([
        ListContactsRequest::class => MockResponse::make(['error' => '200'], 403),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->contacts()->list())
        ->toThrow(SmstoolsException::class);
});

// ─── remove() ─────────────────────────────────────────────────────────────

it('removes a contact by ID', function (): void {
    $mockClient = new MockClient([
        RemoveContactRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->contacts()->remove(42);

    expect($result)->toBeArray()->toHaveKey('success', true);

    $mockClient->assertSent(function (RemoveContactRequest $request): bool {
        return str_ends_with($request->resolveEndpoint(), '/42');
    });
});

it('throws InvalidArgumentException when ID is zero for remove', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->contacts()->remove(0))
        ->toThrow(\InvalidArgumentException::class, 'Contact ID must be a positive integer.');
});
