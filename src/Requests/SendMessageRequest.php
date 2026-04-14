<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class SendMessageRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param string|array<int, string> $to
     */
    public function __construct(
        private readonly string|array $to,
        private readonly string $message,
        private readonly string $sender,
        private readonly ?string $date = null,
        private readonly ?string $reference = null,
        private readonly bool $test = false,
        private readonly ?int $subid = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/message/send';
    }

    protected function defaultBody(): array
    {
        $body = [
            'to'      => $this->to,
            'message' => $this->message,
            'sender'  => $this->sender,
        ];

        if ($this->date !== null) {
            $body['date'] = $this->date;
        }

        if ($this->reference !== null) {
            $body['reference'] = $this->reference;
        }

        if ($this->test) {
            $body['test'] = true;
        }

        if ($this->subid !== null) {
            $body['subid'] = $this->subid;
        }

        return $body;
    }
}
