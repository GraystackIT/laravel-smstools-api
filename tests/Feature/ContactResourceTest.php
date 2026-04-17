<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
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
        firstname: 'Jane',
        number:    '436501234567',
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
        firstname: 'Jane',
        number:    '436501234567',
        lastname:  'Doe',
        groupid:   5,
    );

    $mockClient->assertSent(function (AddContactRequest $request): bool {
        $body = $request->body()->all();

        return $body['firstname'] === 'Jane'
            && $body['number'] === '436501234567'
            && $body['lastname'] === 'Doe'
            && $body['groupid'] === 5;
    });
});

it('throws InvalidArgumentException when firstname is empty for add', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->contacts()->add(
        firstname: '',
        number:    '436501234567',
    ))->toThrow(\InvalidArgumentException::class, 'Contact firstname must not be empty.');
});

it('throws InvalidArgumentException when number is empty for add', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->contacts()->add(
        firstname: 'Jane',
        number:    '',
    ))->toThrow(\InvalidArgumentException::class, 'Contact number must not be empty.');
});

it('throws SmstoolsException on API error during add', function (): void {
    $mockClient = new MockClient([
        AddContactRequest::class => MockResponse::make(['error' => '104'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->contacts()->add(
        firstname: 'Jane',
        number:    '436501234567',
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
        number:    '436509876543',
    );

    $mockClient->assertSent(function (UpdateContactRequest $request): bool {
        $body = $request->body()->all();

        return $body['id'] === 42
            && $body['firstname'] === 'John'
            && $body['number'] === '436509876543'
            && ! array_key_exists('lastname', $body)
            && ! array_key_exists('groupid', $body);
    });
});

it('throws InvalidArgumentException when ID is zero for update', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->contacts()->update(id: 0))
        ->toThrow(\InvalidArgumentException::class, 'Contact ID must be a positive integer.');
});

// ─── search() ─────────────────────────────────────────────────────────────

it('searches contacts by query term', function (): void {
    $mockClient = new MockClient([
        SearchContactRequest::class => MockResponse::make([
            'contacts' => [['id' => 1, 'firstname' => 'Jane', 'number' => '436501234567']],
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->contacts()->search(query: 'Jane');

    expect($result)->toBeArray()->toHaveKey('contacts');
});

it('throws InvalidArgumentException when search query is empty', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->contacts()->search(query: ''))
        ->toThrow(\InvalidArgumentException::class, 'Search query must not be empty.');
});

it('validates page and limit parameters for search', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->contacts()->search(
        query: 'Jane',
        limit: 9999,
    ))->toThrow(\InvalidArgumentException::class, 'Limit must be between 1 and 2000.');

    expect(fn () => (new SmstoolsClient(makeConnector()))->contacts()->search(
        query: 'Jane',
        page:  0,
    ))->toThrow(\InvalidArgumentException::class, 'Page must be at least 1.');
});

// ─── list() ───────────────────────────────────────────────────────────────

it('lists all contacts', function (): void {
    $mockClient = new MockClient([
        ListContactsRequest::class => MockResponse::make([
            'contacts' => [
                ['id' => 1, 'firstname' => 'Alice', 'number' => '436501111111'],
                ['id' => 2, 'firstname' => 'Bob',   'number' => '436502222222'],
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
    expect(fn () => (new SmstoolsClient(makeConnector()))->contacts()->remove(0))
        ->toThrow(\InvalidArgumentException::class, 'Contact ID must be a positive integer.');
});
