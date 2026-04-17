<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Account;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetStatisticsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly ?string $year = null,
        private readonly ?string $month = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/statistics';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        $query = [];

        if ($this->year !== null) {
            $query['year'] = $this->year;
        }

        if ($this->month !== null) {
            $query['month'] = $this->month;
        }

        return $query;
    }
}
