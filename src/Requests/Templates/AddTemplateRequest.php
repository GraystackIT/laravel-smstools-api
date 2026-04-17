<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Templates;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class AddTemplateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $name,
        private readonly string $template,
    ) {
        if (trim($this->name) === '') {
            throw new \InvalidArgumentException('Template name must not be empty.');
        }

        if (trim($this->template) === '') {
            throw new \InvalidArgumentException('Template body must not be empty.');
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
        return [
            'name'     => $this->name,
            'template' => $this->template,
        ];
    }
}
