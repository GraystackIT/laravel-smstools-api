<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Facades;

use GraystackIT\SmstoolsApi\Resources\MessageResource;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MessageResource messages()
 *
 * @see SmstoolsClient
 */
class Smstools extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SmstoolsClient::class;
    }
}
