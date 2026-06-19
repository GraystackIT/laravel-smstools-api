<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Contacts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/** PATCH /contact — update fields on an existing contact. */
class UpdateContactRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  int                    $id            Contact ID to update
     * @param  string|null            $phone         New phone number
     * @param  string|null            $firstname     New first name
     * @param  string|null            $lastname      New last name
     * @param  int|null               $groupid       New group assignment
     * @param  string|null            $birthday      Birthday in yyyy-MM-dd format
     * @param  bool|null              $unsubscribed  Subscription status flag
     * @param  array<string, string>  $extra         Custom fields: extra1–extra8
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private readonly int     $id,
        private readonly ?string $phone = null,
        private readonly ?string $firstname = null,
        private readonly ?string $lastname = null,
        private readonly ?int    $groupid = null,
        private readonly ?string $birthday = null,
        private readonly ?bool   $unsubscribed = null,
        private readonly array   $extra = [],
    ) {
        if ($this->id <= 0) {
            throw new \InvalidArgumentException('Contact ID must be a positive integer.');
        }

        $validKeys = array_map(static fn (int $i): string => "extra{$i}", range(1, 8));

        foreach (array_keys($this->extra) as $key) {
            if (! in_array($key, $validKeys, true)) {
                throw new \InvalidArgumentException("Invalid extra field key '{$key}'. Allowed: extra1–extra8.");
            }
        }
    }

    /** @return string */
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

        if ($this->phone !== null) {
            $body['phone'] = $this->phone;
        }

        if ($this->firstname !== null) {
            $body['firstname'] = $this->firstname;
        }

        if ($this->lastname !== null) {
            $body['lastname'] = $this->lastname;
        }

        if ($this->groupid !== null) {
            $body['groupid'] = $this->groupid;
        }

        if ($this->birthday !== null) {
            $body['birthday'] = $this->birthday;
        }

        if ($this->unsubscribed !== null) {
            $body['unsubscribed'] = $this->unsubscribed;
        }

        foreach ($this->extra as $key => $value) {
            $body[$key] = $value;
        }

        return $body;
    }
}
