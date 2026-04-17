<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Resources;

use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Optouts\AddOptoutRequest;
use GraystackIT\SmstoolsApi\Requests\Optouts\ListOptoutsRequest;
use GraystackIT\SmstoolsApi\Requests\Optouts\RemoveOptoutRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;

class OptoutResource
{
    public function __construct(private readonly SmstoolsClient $client) {}

    /**
     * List all opt-out numbers.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function list(int $limit = 100, int $page = 1): array
    {
        return $this->client->send(new ListOptoutsRequest(
            limit: $limit,
            page:  $page,
        ));
    }

    /**
     * Add a number to the opt-out list.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function add(string $number): array
    {
        return $this->client->send(new AddOptoutRequest(number: $number));
    }

    /**
     * Remove a number from the opt-out list.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function remove(string $number): array
    {
        return $this->client->send(new RemoveOptoutRequest(number: $number));
    }
}
