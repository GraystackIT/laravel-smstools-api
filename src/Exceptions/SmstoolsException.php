<?php

declare(strict_types=1);

namespace Graystack\SmstoolsApi\Exceptions;

use RuntimeException;

class SmstoolsException extends RuntimeException
{
    private const ERROR_MESSAGES = [
        102 => 'No message provided.',
        103 => 'Phone number is required or invalid.',
        104 => 'Invalid credentials.',
        105 => 'Server error. Please try again later.',
        106 => 'Destination number is missing or invalid.',
        108 => 'Not enough credits.',
        109 => 'Sender name cannot be empty.',
        110 => 'Sender number must start with a +.',
        111 => 'Invalid sender name. Maximum 11 characters or 14 digits.',
        112 => 'Message is required.',
        113 => 'Order is required.',
        114 => 'Empty request.',
        116 => 'Access disabled due to long-term inactivity. Contact support.',
        118 => 'SMS message cannot be longer than 612 characters.',
        121 => 'Scheduled date is invalid or in the past.',
        122 => 'No contacts provided.',
        123 => 'Object is missing an ID.',
        124 => 'Parameter groupid is required.',
        125 => 'Group not found.',
        126 => 'Name is required.',
        128 => 'Group not found.',
        129 => 'Number already exists in group.',
        131 => 'This number is on the opt-out list.',
        132 => 'Inbox not found.',
        133 => 'Message not found.',
        134 => 'Webhook ID not found.',
        135 => 'The limit parameter must be an integer between 1 and 2000.',
        136 => 'The page parameter must be an integer greater than 0.',
        137 => 'The 24H window is not open. Send a template message instead.',
        138 => 'Unable to send message due to misconfiguration. Contact support.',
        139 => 'Missing or invalid parameters.',
        140 => 'Invalid or missing WhatsApp Business account.',
        200 => 'Access denied: your IP address is not allowed.',
        1002 => 'Too many failed login attempts.',
        1403 => 'Unauthorized: this team user does not have access to the specified items.',
    ];

    private function __construct(
        string $message,
        private readonly int $statusCode,
        private readonly ?int $apiErrorCode = null,
    ) {
        parent::__construct($message);
    }

    public static function fromResponse(array $body, int $statusCode): self
    {
        $apiErrorCode = isset($body['error']) && is_numeric($body['error'])
            ? (int) $body['error']
            : null;

        if ($apiErrorCode !== null && isset(self::ERROR_MESSAGES[$apiErrorCode])) {
            $message = self::ERROR_MESSAGES[$apiErrorCode];
        } elseif (isset($body['errorMsg'])) {
            $message = $body['errorMsg'];
        } elseif (isset($body['error']) && is_string($body['error'])) {
            $message = $body['error'];
        } else {
            $message = "Smstools API error (HTTP {$statusCode}).";
        }

        return new self($message, $statusCode, $apiErrorCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getApiErrorCode(): ?int
    {
        return $this->apiErrorCode;
    }
}
