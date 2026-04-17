<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Resources;

use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\Contacts\AddContactRequest;
use GraystackIT\SmstoolsApi\Requests\Contacts\ListContactsRequest;
use GraystackIT\SmstoolsApi\Requests\Contacts\RemoveContactRequest;
use GraystackIT\SmstoolsApi\Requests\Contacts\SearchContactRequest;
use GraystackIT\SmstoolsApi\Requests\Contacts\UpdateContactRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;

class ContactResource
{
    public function __construct(private readonly SmstoolsClient $client) {}

    /**
     * Add a new contact.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function add(
        string  $firstname,
        string  $number,
        ?string $lastname = null,
        ?int    $groupid = null,
    ): array {
        return $this->client->send(new AddContactRequest(
            firstname: $firstname,
            number:    $number,
            lastname:  $lastname,
            groupid:   $groupid,
        ));
    }

    /**
     * Update an existing contact.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function update(
        int     $id,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $number = null,
        ?int    $groupid = null,
    ): array {
        return $this->client->send(new UpdateContactRequest(
            id:        $id,
            firstname: $firstname,
            lastname:  $lastname,
            number:    $number,
            groupid:   $groupid,
        ));
    }

    /**
     * Search contacts by name or number.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function search(
        string $query,
        ?int   $groupid = null,
        int    $limit = 100,
        int    $page = 1,
    ): array {
        return $this->client->send(new SearchContactRequest(
            searchQuery: $query,
            groupid:     $groupid,
            limit:       $limit,
            page:        $page,
        ));
    }

    /**
     * List all contacts, optionally filtered by group.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function list(
        ?int $groupid = null,
        int  $limit = 100,
        int  $page = 1,
    ): array {
        return $this->client->send(new ListContactsRequest(
            groupid: $groupid,
            limit:   $limit,
            page:    $page,
        ));
    }

    /**
     * Remove a contact by ID.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function remove(int $id): array
    {
        return $this->client->send(new RemoveContactRequest(id: $id));
    }
}
