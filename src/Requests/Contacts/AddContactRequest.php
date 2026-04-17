<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Contacts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class AddContactRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string  $firstname,
        private readonly string  $number,
        private readonly ?string $lastname = null,
        private readonly ?int    $groupid = null,
    ) {
        if (trim($this->firstname) === '') {
            throw new \InvalidArgumentException('Contact firstname must not be empty.');
        }

        if (trim($this->number) === '') {
            throw new \InvalidArgumentException('Contact number must not be empty.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/contact';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $body = [
            'firstname' => $this->firstname,
            'number'    => $this->number,
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
