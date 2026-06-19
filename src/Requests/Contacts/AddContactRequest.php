<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Contacts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/** POST /contact — create a new contact in the address book. */
class AddContactRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string                 $phone         International phone number of the contact
     * @param  int                    $groupid       Group the contact belongs to
     * @param  string|null            $firstname     Contact's first name
     * @param  string|null            $lastname      Contact's last name
     * @param  string|null            $birthday      Birthday in yyyy-MM-dd format
     * @param  bool|null              $unsubscribed  Subscription status flag
     * @param  array<string, string>  $extra         Custom fields: extra1–extra8
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private readonly string  $phone,
        private readonly int     $groupid,
        private readonly ?string $firstname = null,
        private readonly ?string $lastname = null,
        private readonly ?string $birthday = null,
        private readonly ?bool   $unsubscribed = null,
        private readonly array   $extra = [],
    ) {
        if (trim($this->phone) === '') {
            throw new \InvalidArgumentException('Contact phone must not be empty.');
        }

        if ($this->groupid <= 0) {
            throw new \InvalidArgumentException('Contact groupid must be a positive integer.');
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
        $body = [
            'phone'   => $this->phone,
            'groupid' => $this->groupid,
        ];

        if ($this->firstname !== null) {
            $body['firstname'] = $this->firstname;
        }

        if ($this->lastname !== null) {
            $body['lastname'] = $this->lastname;
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
