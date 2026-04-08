<?php

declare(strict_types=1);

namespace Graystack\SmstoolsApi\Facades;

use Graystack\SmstoolsApi\Resources\MessageResource;
use Graystack\SmstoolsApi\SmstoolsClient;
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
