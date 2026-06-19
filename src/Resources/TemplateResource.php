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

/** Resource for managing reusable message templates. */
class TemplateResource
{
    public function __construct(private readonly SmstoolsClient $client) {}

    /**
     * Add a new message template.
     *
     * @param  string       $message  Template text (supports placeholders, e.g. [FIRSTNAME])
     * @param  int          $order    Sort order in the UI (≥ 1)
     * @param  string|null  $title    Display label for the template
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     * @throws SmstoolsException
     */
    public function add(string $message, int $order, ?string $title = null): array
    {
        return $this->client->send(new AddTemplateRequest(
            message: $message,
            order:   $order,
            title:   $title,
        ));
    }

    /**
     * Update an existing message template.
     *
     * @param  int          $id       Template ID
     * @param  string|null  $message  New template text
     * @param  string|null  $title    New display label
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     * @throws SmstoolsException
     */
    public function update(
        int     $id,
        ?string $message = null,
        ?string $title = null,
    ): array {
        return $this->client->send(new UpdateTemplateRequest(
            id:      $id,
            message: $message,
            title:   $title,
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
     * @param  int  $id  Template ID
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     * @throws SmstoolsException
     */
    public function get(int $id): array
    {
        return $this->client->send(new GetTemplateRequest(id: $id));
    }

    /**
     * Remove a message template by ID.
     *
     * @param  int  $id  Template ID
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     * @throws SmstoolsException
     */
    public function remove(int $id): array
    {
        return $this->client->send(new RemoveTemplateRequest(id: $id));
    }
}
