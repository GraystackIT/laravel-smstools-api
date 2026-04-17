<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\BirthdayMessages;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListBirthdayMessagesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/birthday-message';
    }
}
