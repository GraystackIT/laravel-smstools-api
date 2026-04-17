<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Templates;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetTemplateRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(private readonly int $id)
    {
        if ($this->id <= 0) {
            throw new \InvalidArgumentException('Template ID must be a positive integer.');
        }
    }

    public function resolveEndpoint(): string
    {
        return "/messagetemplates/{$this->id}";
    }
}
