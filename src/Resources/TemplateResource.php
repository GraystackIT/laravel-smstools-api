<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Resources;

use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Templates\AddTemplateRequest;
use GraystackIT\SmstoolsApi\Requests\Templates\GetTemplateRequest;
use GraystackIT\SmstoolsApi\Requests\Templates\ListTemplatesRequest;
use GraystackIT\SmstoolsApi\Requests\Templates\RemoveTemplateRequest;
use GraystackIT\SmstoolsApi\Requests\Templates\UpdateTemplateRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;

class TemplateResource
{
    public function __construct(private readonly SmstoolsClient $client) {}

    /**
     * Add a new message template.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function add(string $name, string $template): array
    {
        return $this->client->send(new AddTemplateRequest(
            name:     $name,
            template: $template,
        ));
    }

    /**
     * Update an existing message template.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function update(
        int     $id,
        ?string $name = null,
        ?string $template = null,
    ): array {
        return $this->client->send(new UpdateTemplateRequest(
            id:       $id,
            name:     $name,
            template: $template,
        ));
    }

    /**
     * List all message templates.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function list(): array
    {
        return $this->client->send(new ListTemplatesRequest());
    }

    /**
     * Retrieve a specific message template by ID.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function get(int $id): array
    {
        return $this->client->send(new GetTemplateRequest(id: $id));
    }

    /**
     * Remove a message template by ID.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function remove(int $id): array
    {
        return $this->client->send(new RemoveTemplateRequest(id: $id));
    }
}
