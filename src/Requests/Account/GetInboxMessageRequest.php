<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Account;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetInboxMessageRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly string $id)
    {
        if (trim($this->id) === '') {
            throw new \InvalidArgumentException('Inbox message ID must not be empty.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/message/inbox/' . $this->id;
    }
}
