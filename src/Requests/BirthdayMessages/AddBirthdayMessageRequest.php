<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\BirthdayMessages;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class AddBirthdayMessageRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string  $firstname,
        private readonly string  $number,
        private readonly string  $birthday,
        private readonly string  $message,
        private readonly string  $sender,
        private readonly ?string $lastname = null,
        private readonly ?int    $groupid = null,
    ) {
        if (trim($this->firstname) === '') {
            throw new \InvalidArgumentException('Firstname must not be empty.');
        }

        if (trim($this->number) === '') {
            throw new \InvalidArgumentException('Number must not be empty.');
        }

        if (trim($this->birthday) === '') {
            throw new \InvalidArgumentException('Birthday must not be empty.');
        }

        if (trim($this->message) === '') {
            throw new \InvalidArgumentException('Message must not be empty.');
        }

        if (trim($this->sender) === '') {
            throw new \InvalidArgumentException('Sender must not be empty.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/birthday-message';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $body = [
            'firstname' => $this->firstname,
            'number'    => $this->number,
            'birthday'  => $this->birthday,
            'message'   => $this->message,
            'sender'    => $this->sender,
        ];

        if ($this->lastname !== null) {
            $body['lastname'] = $this->lastname;
        }

        if ($this->groupid !== null) {
            $body['groupid'] = $this->groupid;
        }

        return $body;
    }
}
