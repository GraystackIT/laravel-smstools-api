<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Contacts;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class RemoveContactRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(private readonly int $id)
    {
        if ($this->id <= 0) {
            throw new \InvalidArgumentException('Contact ID must be a positive integer.');
        }
    }

    public function resolveEndpoint(): string
    {
        return "/contact/{$this->id}";
    }
}
