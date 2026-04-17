<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Templates;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateTemplateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        private readonly int     $id,
        private readonly ?string $name = null,
        private readonly ?string $template = null,
    ) {
        if ($this->id <= 0) {
            throw new \InvalidArgumentException('Template ID must be a positive integer.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/messagetemplates';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $body = ['id' => $this->id];

        if ($this->name !== null) {
            $body['name'] = $this->name;
        }

        if ($this->template !== null) {
            $body['template'] = $this->template;
        }

        return $body;
    }
}
