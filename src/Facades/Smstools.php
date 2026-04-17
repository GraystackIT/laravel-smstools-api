<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Facades;

use GraystackIT\SmstoolsApi\Resources\AccountResource;
use GraystackIT\SmstoolsApi\Resources\BirthdayMessageResource;
use GraystackIT\SmstoolsApi\Resources\ContactResource;
use GraystackIT\SmstoolsApi\Resources\MessageResource;
use GraystackIT\SmstoolsApi\Resources\OptoutResource;
use GraystackIT\SmstoolsApi\Resources\TemplateResource;
use GraystackIT\SmstoolsApi\Resources\WebhookResource;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MessageResource         messages()
 * @method static ContactResource         contacts()
 * @method static AccountResource         account()
 * @method static TemplateResource        templates()
 * @method static BirthdayMessageResource birthdayMessages()
 * @method static OptoutResource          optouts()
 * @method static WebhookResource         webhooks()
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
