<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Templates;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/** PATCH /messagetemplates — update fields on an existing message template. */
class UpdateTemplateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    /**
     * @param  int          $id       Template ID to update
     * @param  string|null  $message  New template text content
     * @param  string|null  $title    New display label
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private readonly int     $id,
        private readonly ?string $message = null,
        private readonly ?string $title = null,
    ) {
        if ($this->id <= 0) {
            throw new \InvalidArgumentException('Template ID must be a positive integer.');
        }
    }

    /** @return string */
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

        if ($this->message !== null) {
            $body['message'] = $this->message;
        }

        if ($this->title !== null) {
            $body['title'] = $this->title;
        }

        return $body;
    }
}
