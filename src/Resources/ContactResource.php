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

/** Resource for managing contacts in the address book. */
class ContactResource
{
    public function __construct(private readonly SmstoolsClient $client) {}

    /**
     * Add a new contact.
     *
     * @param  string                 $phone         International phone number (required)
     * @param  int                    $groupid       Group the contact belongs to (required)
     * @param  string|null            $firstname     First name
     * @param  string|null            $lastname      Last name
     * @param  string|null            $birthday      Birthday in yyyy-MM-dd format
     * @param  bool|null              $unsubscribed  Subscription status
     * @param  array<string, string>  $extra         Custom fields: extra1–extra8
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     * @throws SmstoolsException
     */
    public function add(
        string  $phone,
        int     $groupid,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $birthday = null,
        ?bool   $unsubscribed = null,
        array   $extra = [],
    ): array {
        return $this->client->send(new AddContactRequest(
            phone:        $phone,
            groupid:      $groupid,
            firstname:    $firstname,
            lastname:     $lastname,
            birthday:     $birthday,
            unsubscribed: $unsubscribed,
            extra:        $extra,
        ));
    }

    /**
     * Update an existing contact.
     *
     * @param  int                    $id            Contact ID to update
     * @param  string|null            $phone         New phone number
     * @param  string|null            $firstname     New first name
     * @param  string|null            $lastname      New last name
     * @param  int|null               $groupid       New group assignment
     * @param  string|null            $birthday      Birthday in yyyy-MM-dd format
     * @param  bool|null              $unsubscribed  Subscription status
     * @param  array<string, string>  $extra         Custom fields: extra1–extra8
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     * @throws SmstoolsException
     */
    public function update(
        int     $id,
        ?string $phone = null,
        ?string $firstname = null,
        ?string $lastname = null,
        ?int    $groupid = null,
        ?string $birthday = null,
        ?bool   $unsubscribed = null,
        array   $extra = [],
    ): array {
        return $this->client->send(new UpdateContactRequest(
            id:           $id,
            phone:        $phone,
            firstname:    $firstname,
            lastname:     $lastname,
            groupid:      $groupid,
            birthday:     $birthday,
            unsubscribed: $unsubscribed,
            extra:        $extra,
        ));
    }

    /**
     * Search contacts by name or phone number.
     *
     * @param  string   $query    Search keyword
     * @param  int|null $groupid  Restrict results to a specific group
     * @param  int      $limit    Results per page (1–2000)
     * @param  int      $page     Page number (≥ 1)
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
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
     * @param  int|null $groupid  Restrict results to a specific group
     * @param  int      $limit    Results per page (1–2000)
     * @param  int      $page     Page number (≥ 1)
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
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
     * @param  int  $id  Contact ID
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     * @throws SmstoolsException
     */
    public function remove(int $id): array
    {
        return $this->client->send(new RemoveContactRequest(id: $id));
    }
}
