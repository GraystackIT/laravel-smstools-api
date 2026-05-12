<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Account;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetInboxByNumberRequest extends Request
{
    protected Method $method = Method::GET;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private readonly int $inboxNr,
        private readonly int $limit = 100,
        private readonly int $page = 1,
        private readonly ?string $type = null,
    ) {
        if ($this->inboxNr <= 0) {
            throw new \InvalidArgumentException('Inbox number must be a positive integer.');
        }

        if ($this->limit < 1 || $this->limit > 2000) {
            throw new \InvalidArgumentException('Limit must be between 1 and 2000.');
        }

        if ($this->page < 1) {
            throw new \InvalidArgumentException('Page must be at least 1.');
        }
    }

    public function resolveEndpoint(): string
    {
        return "/message/inbox/{$this->inboxNr}";
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        $query = [
            'limit' => $this->limit,
            'page' => $this->page,
        ];

        if ($this->type !== null) {
            $query['type'] = $this->type;
        }

        return $query;
    }
}
