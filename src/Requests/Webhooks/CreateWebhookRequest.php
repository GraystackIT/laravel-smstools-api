<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Webhooks;

use GraystackIT\SmstoolsApi\Enums\WebhookMethod;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateWebhookRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, string>  $webhookTypes  Event types this webhook should receive.
     */
    public function __construct(
        private readonly string        $webhookEndpoint,
        private readonly WebhookMethod $webhookMethod,
        private readonly array         $webhookTypes,
    ) {
        if (filter_var($this->webhookEndpoint, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException(
                "Invalid webhook endpoint URL: {$this->webhookEndpoint}"
            );
        }

        if (empty($this->webhookTypes)) {
            throw new \InvalidArgumentException('webhook_types must contain at least one event type.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/webhook/create';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'webhook_endpoint' => $this->webhookEndpoint,
            'webhook_method'   => $this->webhookMethod->value,
            'webhook_types'    => $this->webhookTypes,
        ];
    }
}
