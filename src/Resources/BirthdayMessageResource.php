<?php

declare(strict_types=1);

namespace GraystackIT\SmstoolsApi\Resources;

use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\AddBirthdayMessageRequest;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\GetBirthdayMessageRequest;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\ListBirthdayMessagesRequest;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\RemoveBirthdayMessageRequest;
use GraystackIT\SmstoolsApi\Requests\BirthdayMessages\UpdateBirthdayMessageRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;

class BirthdayMessageResource
{
    public function __construct(private readonly SmstoolsClient $client) {}

    /**
     * Schedule a birthday SMS for a contact.
     *
     * @param  string  $birthday  Date in "MM-dd" format (e.g. "06-15")
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function add(
        string  $firstname,
        string  $number,
        string  $birthday,
        string  $message,
        string  $sender,
        ?string $lastname = null,
        ?int    $groupid = null,
    ): array {
        return $this->client->send(new AddBirthdayMessageRequest(
            firstname: $firstname,
            number:    $number,
            birthday:  $birthday,
            message:   $message,
            sender:    $sender,
            lastname:  $lastname,
            groupid:   $groupid,
        ));
    }

    /**
     * List all birthday messages.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function list(): array
    {
        return $this->client->send(new ListBirthdayMessagesRequest());
    }

    /**
     * Retrieve a specific birthday message by ID.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function get(int $id): array
    {
        return $this->client->send(new GetBirthdayMessageRequest(id: $id));
    }

    /**
     * Update an existing birthday message.
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
        ?string $birthday = null,
        ?string $message = null,
        ?string $sender = null,
        ?int    $groupid = null,
    ): array {
        return $this->client->send(new UpdateBirthdayMessageRequest(
            id:        $id,
            firstname: $firstname,
            lastname:  $lastname,
            number:    $number,
            birthday:  $birthday,
            message:   $message,
            sender:    $sender,
            groupid:   $groupid,
        ));
    }

    /**
     * Remove a birthday message by ID.
     *
     * @return array<string, mixed>
     *
     * @throws SmstoolsException
     */
    public function remove(int $id): array
    {
        return $this->client->send(new RemoveBirthdayMessageRequest(id: $id));
    }
}
