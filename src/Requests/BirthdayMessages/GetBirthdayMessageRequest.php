<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\BirthdayMessages;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetBirthdayMessageRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly int $id)
    {
        if ($this->id <= 0) {
            throw new \InvalidArgumentException('Birthday message ID must be a positive integer.');
        }
    }

    public function resolveEndpoint(): string
    {
        return "/birthday-message/{$this->id}";
    }
}
