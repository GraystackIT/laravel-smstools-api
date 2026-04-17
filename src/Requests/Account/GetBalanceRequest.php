<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Account;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetBalanceRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/balance';
    }
}
