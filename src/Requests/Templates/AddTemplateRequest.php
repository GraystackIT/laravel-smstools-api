<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Templates;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/** POST /messagetemplates — create a new message template. */
class AddTemplateRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  string       $message  Template text content (supports placeholder syntax, e.g. [FIRSTNAME])
     * @param  int          $order    Sort order of the template in the UI (≥ 1)
     * @param  string|null  $title    Display label for the template
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private readonly string  $message,
        private readonly int     $order,
        private readonly ?string $title = null,
    ) {
        if (trim($this->message) === '') {
            throw new \InvalidArgumentException('Template message must not be empty.');
        }

        if ($this->order < 1) {
            throw new \InvalidArgumentException('Template order must be a positive integer.');
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
        $body = [
            'message' => $this->message,
            'order'   => $this->order,
        ];

        if ($this->title !== null) {
            $body['title'] = $this->title;
        }

        return $body;
    }
}
