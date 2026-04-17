<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Optouts;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class RemoveOptoutRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(private readonly string $number)
    {
        if (trim($this->number) === '') {
            throw new \InvalidArgumentException('Opt-out number must not be empty.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/optouts/' . rawurlencode($this->number);
    }
}
