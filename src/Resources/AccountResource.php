<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Resources;

use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Account\GetAccountRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetBalanceRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetHistoryRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetInboxMessageRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetInboxRequest;
use GraystackIT\SmstoolsApi\Requests\Account\GetStatisticsRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;

class AccountResource
{
    public function __construct(private readonly SmstoolsClient $client) {}

    /**
     * Retrieve account details.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function details(): array
    {
        return $this->client->send(new GetAccountRequest());
    }

    /**
     * Retrieve the current account credit balance.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function balance(): array
    {
        return $this->client->send(new GetBalanceRequest());
    }

    /**
     * Retrieve account transaction history.
     *
     * @param  string|null  $from  Start date in "yyyy-MM-dd" format
     * @param  string|null  $to    End date in "yyyy-MM-dd" format
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function history(
        ?string $from = null,
        ?string $to = null,
        int     $limit = 100,
        int     $page = 1,
    ): array {
        return $this->client->send(new GetHistoryRequest(
            from:  $from,
            to:    $to,
            limit: $limit,
            page:  $page,
        ));
    }

    /**
     * Retrieve all inbox messages.
     *
     * @param  string|null  $type  Filter by message type: "sms", "whatsapp", or "call"
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function inbox(
        int     $limit = 100,
        int     $page = 1,
        ?string $type = null,
    ): array {
        return $this->client->send(new GetInboxRequest(
            limit: $limit,
            page:  $page,
            type:  $type,
        ));
    }

    /**
     * Retrieve a specific inbox message by ID.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function inboxMessage(string $id): array
    {
        return $this->client->send(new GetInboxMessageRequest(id: $id));
    }

    /**
     * Retrieve account SMS statistics.
     *
     * @param  string|null  $year   Four-digit year (e.g. "2024")
     * @param  string|null  $month  Two-digit month (e.g. "01")
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function statistics(
        ?string $year = null,
        ?string $month = null,
    ): array {
        return $this->client->send(new GetStatisticsRequest(
            year:  $year,
            month: $month,
        ));
    }
}
