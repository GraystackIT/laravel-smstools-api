<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Webhooks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteWebhookRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(private readonly string $secret)
    {
        if (trim($this->secret) === '') {
            throw new \InvalidArgumentException('Webhook secret must not be empty.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/webhook/' . rawurlencode($this->secret);
    }
}
