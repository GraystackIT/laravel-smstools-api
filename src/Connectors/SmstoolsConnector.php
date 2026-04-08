<?php

declare(strict_types=1);

namespace Graystack\SmstoolsApi\Connectors;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class SmstoolsConnector extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $baseUrl = 'https://api.smsgatewayapi.com/v1',
    ) {}

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function defaultHeaders(): array
    {
        return [
            'X-Client-Id'     => $this->clientId,
            'X-Client-Secret' => $this->clientSecret,
        ];
    }
}
