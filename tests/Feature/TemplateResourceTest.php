<?php

declare(strict_types=1);

use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
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
        name:     'Welcome',
        template: 'Hello {firstname}, welcome!',
    );

    expect($result)->toBeArray()->toHaveKey('id', 10);

    $mockClient->assertSent(function (AddTemplateRequest $request): bool {
        $body = $request->body()->all();

        return $body['name'] === 'Welcome'
            && $body['template'] === 'Hello {firstname}, welcome!';
    });
});

it('throws InvalidArgumentException when template name is empty', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->templates()->add(
        name:     '',
        template: 'Hello',
    ))->toThrow(\InvalidArgumentException::class, 'Template name must not be empty.');
});

it('throws InvalidArgumentException when template body is empty', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->templates()->add(
        name:     'My Template',
        template: '',
    ))->toThrow(\InvalidArgumentException::class, 'Template body must not be empty.');
});

it('throws SmstoolsException on API error during add', function (): void {
    $mockClient = new MockClient([
        AddTemplateRequest::class => MockResponse::make(['error' => '139'], 400),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    expect(fn () => (new SmstoolsClient($connector))->templates()->add('X', 'Y'))
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
        id:       10,
        template: 'Updated body',
    );

    $mockClient->assertSent(function (UpdateTemplateRequest $request): bool {
        $body = $request->body()->all();

        return $body['id'] === 10
            && $body['template'] === 'Updated body'
            && ! array_key_exists('name', $body);
    });
});

it('throws InvalidArgumentException when template ID is zero for update', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->templates()->update(id: 0))
        ->toThrow(\InvalidArgumentException::class, 'Template ID must be a positive integer.');
});

// ─── list() ───────────────────────────────────────────────────────────────

it('lists all templates', function (): void {
    $mockClient = new MockClient([
        ListTemplatesRequest::class => MockResponse::make([
            'templates' => [
                ['id' => 1, 'name' => 'Welcome'],
                ['id' => 2, 'name' => 'Reminder'],
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
            'id'       => 1,
            'name'     => 'Welcome',
            'template' => 'Hello {firstname}!',
        ], 200),
    ]);

    $connector = makeConnector();
    $connector->withMockClient($mockClient);

    $result = (new SmstoolsClient($connector))->templates()->get(1);

    expect($result)->toBeArray()
        ->toHaveKey('id', 1)
        ->toHaveKey('name', 'Welcome');

    $mockClient->assertSent(GetTemplateRequest::class);
});

it('throws InvalidArgumentException when template ID is zero for get', function (): void {
    expect(fn () => (new SmstoolsClient(makeConnector()))->templates()->get(0))
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
    expect(fn () => (new SmstoolsClient(makeConnector()))->templates()->remove(0))
        ->toThrow(\InvalidArgumentException::class, 'Template ID must be a positive integer.');
});
