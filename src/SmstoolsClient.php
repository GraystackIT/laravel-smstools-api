<?php

declare(strict_types=1);

namespace Graystack\SmstoolsApi;

use Graystack\SmstoolsApi\Connectors\SmstoolsConnector;
use Graystack\SmstoolsApi\Exceptions\SmstoolsException;
use Graystack\SmstoolsApi\Resources\MessageResource;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Request;

class SmstoolsClient
{
    private ?MessageResource $messageResource = null;

    public function __construct(
        private readonly SmstoolsConnector $connector,
    ) {}

    public function messages(): MessageResource
    {
        if ($this->messageResource === null) {
            $this->messageResource = new MessageResource($this);
        }

        return $this->messageResource;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function send(Request $request): array
    {
        try {
            $response = $this->connector->send($request);
        } catch (RequestException $e) {
            throw SmstoolsException::fromResponse(
                $e->getResponse()->json() ?? [],
                $e->getResponse()->status(),
            );
        }

        return $response->json() ?? [];
    }

    public function getConnector(): SmstoolsConnector
    {
        return $this->connector;
    }
}
