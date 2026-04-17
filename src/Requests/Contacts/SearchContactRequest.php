<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Requests\Contacts;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class SearchContactRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $searchQuery,
        private readonly ?int   $groupid = null,
        private readonly int    $limit = 100,
        private readonly int    $page = 1,
    ) {
        if (trim($this->searchQuery) === '') {
            throw new \InvalidArgumentException('Search query must not be empty.');
        }

        if ($this->limit < 1 || $this->limit > 2000) {
            throw new \InvalidArgumentException('Limit must be between 1 and 2000.');
        }

        if ($this->page < 1) {
            throw new \InvalidArgumentException('Page must be at least 1.');
        }
    }

    public function resolveEndpoint(): string
    {
        return '/contact/search/' . rawurlencode($this->searchQuery);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        $query = [
            'limit' => $this->limit,
            'page'  => $this->page,
        ];

        if ($this->groupid !== null) {
            $query['groupid'] = $this->groupid;
        }

        return $query;
    }
}
