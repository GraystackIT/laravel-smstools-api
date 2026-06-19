<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Templates\AddTemplateRequest;
use GraystackIT\SmstoolsApi\Requests\Templates\GetTemplateRequest;
use GraystackIT\SmstoolsApi\Requests\Templates\ListTemplatesRequest;
use GraystackIT\SmstoolsApi\Requests\Templates\RemoveTemplateRequest;
use GraystackIT\SmstoolsApi\Requests\Templates\UpdateTemplateRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

// ─── add() ────────────────────────────────────────────────────────────────

it('adds a message template', function (): void {
    $mockClient = new MockClient([
        AddTemplateRequest::class => MockResponse::make(['id' => 10], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->templates()->add(
        message: 'Hello {firstname}, welcome!',
        order:   1,
        title:   'Welcome',
    );

    expect($result)->toBeArray()->toHaveKey('id', 10);

    $mockClient->assertSent(function (AddTemplateRequest $request): bool {
        $body = $request->body()->all();

        return $body['message'] === 'Hello {firstname}, welcome!'
            && $body['order'] === 1
            && $body['title'] === 'Welcome';
    });
});

it('adds a message template without a title', function (): void {
    $mockClient = new MockClient([
        AddTemplateRequest::class => MockResponse::make(['id' => 11], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->templates()->add(
        message: 'Reminder: your appointment is tomorrow.',
        order:   2,
    );

    $mockClient->assertSent(function (AddTemplateRequest $request): bool {
        $body = $request->body()->all();

        return $body['message'] === 'Reminder: your appointment is tomorrow.'
            && $body['order'] === 2
            && ! array_key_exists('title', $body);
    });
});

it('throws InvalidArgumentException when template message is empty', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->templates()->add(
        message: '',
        order:   1,
    ))->toThrow(\InvalidArgumentException::class, 'Template message must not be empty.');
});

it('throws InvalidArgumentException when template order is invalid', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->templates()->add(
        message: 'Hello',
        order:   0,
    ))->toThrow(\InvalidArgumentException::class, 'Template order must be a positive integer.');
});

it('throws SmstoolsException on API error during add', function (): void {
    $mockClient = new MockClient([
        AddTemplateRequest::class => MockResponse::make(['error' => '139'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->templates()->add('Hello', 1))
        ->toThrow(SmstoolsException::class, 'Missing or invalid parameters.');
});

// ─── update() ─────────────────────────────────────────────────────────────

it('updates a template with only changed fields', function (): void {
    $mockClient = new MockClient([
        UpdateTemplateRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->templates()->update(
        id:      10,
        message: 'Updated body',
    );

    $mockClient->assertSent(function (UpdateTemplateRequest $request): bool {
        $body = $request->body()->all();

        return $body['id'] === 10
            && $body['message'] === 'Updated body'
            && ! array_key_exists('title', $body);
    });
});

it('updates a template title only', function (): void {
    $mockClient = new MockClient([
        UpdateTemplateRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    (new SmstoolsClient($connector))->templates()->update(
        id:    5,
        title: 'Renamed Template',
    );

    $mockClient->assertSent(function (UpdateTemplateRequest $request): bool {
        $body = $request->body()->all();

        return $body['id'] === 5
            && $body['title'] === 'Renamed Template'
            && ! array_key_exists('message', $body);
    });
});

it('throws InvalidArgumentException when template ID is zero for update', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->templates()->update(id: 0))
        ->toThrow(\InvalidArgumentException::class, 'Template ID must be a positive integer.');
});

// ─── list() ───────────────────────────────────────────────────────────────

it('lists all templates', function (): void {
    $mockClient = new MockClient([
        ListTemplatesRequest::class => MockResponse::make([
            'templates' => [
                ['id' => 1, 'title' => 'Welcome', 'message' => 'Hello!'],
                ['id' => 2, 'title' => 'Reminder', 'message' => 'Don\'t forget!'],
            ],
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->templates()->list();

    expect($result)->toBeArray()->toHaveKey('templates');
    expect($result['templates'])->toHaveCount(2);

    $mockClient->assertSent(ListTemplatesRequest::class);
});

it('throws SmstoolsException on API error during list', function (): void {
    $mockClient = new MockClient([
        ListTemplatesRequest::class => MockResponse::make(['error' => '104'], 401),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->templates()->list())
        ->toThrow(SmstoolsException::class);
});

// ─── get() ────────────────────────────────────────────────────────────────

it('retrieves a specific template by ID', function (): void {
    $mockClient = new MockClient([
        GetTemplateRequest::class => MockResponse::make([
            'id'      => 1,
            'title'   => 'Welcome',
            'message' => 'Hello {firstname}!',
            'order'   => 1,
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->templates()->get(1);

    expect($result)->toBeArray()
        ->toHaveKey('id', 1)
        ->toHaveKey('title', 'Welcome');

    $mockClient->assertSent(GetTemplateRequest::class);
});

it('throws InvalidArgumentException when template ID is zero for get', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->templates()->get(0))
        ->toThrow(\InvalidArgumentException::class, 'Template ID must be a positive integer.');
});

// ─── remove() ─────────────────────────────────────────────────────────────

it('removes a template by ID', function (): void {
    $mockClient = new MockClient([
        RemoveTemplateRequest::class => MockResponse::make(['success' => true], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->templates()->remove(1);

    expect($result)->toBeArray()->toHaveKey('success', true);

    $mockClient->assertSent(function (RemoveTemplateRequest $request): bool {
        return str_ends_with($request->resolveEndpoint(), '/1');
    });
});

it('throws InvalidArgumentException when template ID is zero for remove', function (): void {
    $connector = makeConnector();
    expect(fn () => (new SmstoolsClient($connector))->templates()->remove(0))
        ->toThrow(\InvalidArgumentException::class, 'Template ID must be a positive integer.');
});
