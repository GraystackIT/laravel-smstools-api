<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Templates;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListTemplatesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/messagetemplates';
    }
}
