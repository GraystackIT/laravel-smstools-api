<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Webhooks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListWebhooksRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/webhook/endpoint-list';
    }
}
