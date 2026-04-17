<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Resources;

use GraystackIT\SmstoolsApi\Enums\WebhookMethod;
use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Webhooks\CreateWebhookRequest;
use GraystackIT\SmstoolsApi\Requests\Webhooks\DeleteWebhookRequest;
use GraystackIT\SmstoolsApi\Requests\Webhooks\ListWebhooksRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;

class WebhookResource
{
    public function __construct(private readonly SmstoolsClient $client) {}

    /**
     * List all registered webhooks.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function list(): array
    {
        return $this->client->send(new ListWebhooksRequest());
    }

    /**
     * Register a new webhook.
     *
     * @param  array<int, string>  $webhookTypes  Event types to subscribe to (e.g. ["sms_inbound", "sms_delivered"]).
     *
     * @return array{secret: string}
     *
     * @throws SmstoolsException
     */
    public function create(
        string        $webhookEndpoint,
        WebhookMethod $webhookMethod,
        array         $webhookTypes,
    ): array {
        return $this->client->send(new CreateWebhookRequest(
            webhookEndpoint: $webhookEndpoint,
            webhookMethod:   $webhookMethod,
            webhookTypes:    $webhookTypes,
        ));
    }

    /**
     * Delete a webhook by its secret token.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function delete(string $secret): array
    {
        return $this->client->send(new DeleteWebhookRequest(secret: $secret));
    }
}
