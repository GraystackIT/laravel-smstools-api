<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Contacts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateContactRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        private readonly int     $id,
        private readonly ?string $firstname = null,
        private readonly ?string $lastname = null,
        private readonly ?string $number = null,
        private readonly ?int    $groupid = null,
    ) {
        if ($this->id <= 0) {
            throw new \InvalidArgumentException('Contact ID must be a positive integer.');
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
        $body = ['id' => $this->id];

        if ($this->firstname !== null) {
            $body['firstname'] = $this->firstname;
        }

        if ($this->lastname !== null) {
            $body['lastname'] = $this->lastname;
        }

        if ($this->number !== null) {
            $body['number'] = $this->number;
        }

        if ($this->groupid !== null) {
            $body['groupid'] = $this->groupid;
        }

        return $body;
    }
}
