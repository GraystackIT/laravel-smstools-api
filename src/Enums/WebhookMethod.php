<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Enums;

enum WebhookMethod: string
{
    case Post = 'POST';
    case Get  = 'GET';
}
