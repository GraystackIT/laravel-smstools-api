<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Connectors;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class SmstoolsConnector extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    /**
     * @throws \InvalidArgumentException when clientId or clientSecret is empty
     */
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
    ) {
        if (empty($this->clientId)) {
            throw new \InvalidArgumentException('Smstools client ID must not be empty.');
        }

        if (empty($this->clientSecret)) {
            throw new \InvalidArgumentException('Smstools client secret must not be empty.');
        }
    }

    public function resolveBaseUrl(): string
    {
        return config('smstools.base_url', 'https://api.smsgatewayapi.com/v1');
    }

    protected function defaultHeaders(): array
    {
        return [
            'X-Client-Id'     => $this->clientId,
            'X-Client-Secret' => $this->clientSecret,
        ];
    }
}
