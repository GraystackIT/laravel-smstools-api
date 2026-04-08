<?php

declare(strict_types=1);

namespace Graystack\SmstoolsApi\Resources;

use Graystack\SmstoolsApi\Exceptions\SmstoolsException;
use Graystack\SmstoolsApi\Requests\SendMessageRequest;
use Graystack\SmstoolsApi\SmstoolsClient;

class MessageResource
{
    public function __construct(
        private readonly SmstoolsClient $client,
    ) {}

    /**
     * Send an SMS message to one or more recipients.
     *
     * @param string|array<int, string> $to   Recipient number(s) in international format (e.g. 436501234567)
     * @param string                    $message  The message body (max 612 characters)
     * @param string                    $sender   Sender name (max 11 chars) or number (max 14 digits)
     * @param string|null               $date     Optional scheduled send time in "yyyy-MM-dd HH:mm" format
     * @param string|null               $reference  Optional custom reference string (max 255 chars)
     * @param bool                      $test     When true, validates parameters without sending (no credits used)
     * @param int|null                  $subid    Optional subaccount ID to send from
     *
     * @return array{messageid: string}|array{messageids: array<int, string>}
     *
     * @throws SmstoolsException
     */
    public function send(
        string|array $to,
        string $message,
        string $sender,
        ?string $date = null,
        ?string $reference = null,
        bool $test = false,
        ?int $subid = null,
    ): array {
        return $this->client->send(new SendMessageRequest(
            to: $to,
            message: $message,
            sender: $sender,
            date: $date,
            reference: $reference,
            test: $test,
            subid: $subid,
        ));
    }
}
