<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi;

use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Resources\AccountResource;
use GraystackIT\SmstoolsApi\Resources\BirthdayMessageResource;
use GraystackIT\SmstoolsApi\Resources\ContactResource;
use GraystackIT\SmstoolsApi\Resources\MessageResource;
use GraystackIT\SmstoolsApi\Resources\OptoutResource;
use GraystackIT\SmstoolsApi\Resources\TemplateResource;
use GraystackIT\SmstoolsApi\Resources\WebhookResource;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Request;

class SmstoolsClient
{
    private ?MessageResource        $messageResource = null;
    private ?ContactResource        $contactResource = null;
    private ?AccountResource        $accountResource = null;
    private ?TemplateResource       $templateResource = null;
    private ?BirthdayMessageResource $birthdayMessageResource = null;
    private ?OptoutResource         $optoutResource = null;
    private ?WebhookResource        $webhookResource = null;

    public function __construct(
        private readonly SmstoolsConnector $connector,
    ) {}

    public function messages(): MessageResource
    {
        if ($this->messageResource === null) {
            $this->messageResource = new MessageResource($this);
        }

        return $this->messageResource;
    }

    public function contacts(): ContactResource
    {
        if ($this->contactResource === null) {
            $this->contactResource = new ContactResource($this);
        }

        return $this->contactResource;
    }

    public function account(): AccountResource
    {
        if ($this->accountResource === null) {
            $this->accountResource = new AccountResource($this);
        }

        return $this->accountResource;
    }

    public function templates(): TemplateResource
    {
        if ($this->templateResource === null) {
            $this->templateResource = new TemplateResource($this);
        }

        return $this->templateResource;
    }

    public function birthdayMessages(): BirthdayMessageResource
    {
        if ($this->birthdayMessageResource === null) {
            $this->birthdayMessageResource = new BirthdayMessageResource($this);
        }

        return $this->birthdayMessageResource;
    }

    public function optouts(): OptoutResource
    {
        if ($this->optoutResource === null) {
            $this->optoutResource = new OptoutResource($this);
        }

        return $this->optoutResource;
    }

    public function webhooks(): WebhookResource
    {
        if ($this->webhookResource === null) {
            $this->webhookResource = new WebhookResource($this);
        }

        return $this->webhookResource;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function send(Request $request): array
    {
        try {
            $response = $this->connector->send($request);
        } catch (RequestException $e) {
            throw SmstoolsException::fromResponse(
                $e->getResponse()->json() ?? [],
                $e->getResponse()->status(),
            );
        }

        return $response->json() ?? [];
    }

    public function getConnector(): SmstoolsConnector
    {
        return $this->connector;
    }
}
