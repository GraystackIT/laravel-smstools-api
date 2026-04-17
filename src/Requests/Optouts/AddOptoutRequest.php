<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Optouts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class AddOptoutRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(private readonly string $number)
    {
        if (trim($this->number) === '') {
            throw new \InvalidArgumentException('Opt-out number must not be empty.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/optouts';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return ['number' => $this->number];
    }
}
