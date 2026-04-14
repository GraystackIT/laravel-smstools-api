# graystackit/laravel-smstools-api

A Laravel package for the [Smstools SMS Gateway API](https://www.smsgatewayapi.com/), built on [Saloon 4](https://docs.saloon.dev/).

Send SMS messages to single or multiple recipients, schedule delivery, use test mode, and route through subaccounts — all with a clean, Laravel-native interface.

## Requirements

- PHP 8.2+
- Laravel 10, 11, or 12

## Installation

```bash
composer require graystackit/laravel-smstools-api
```

Laravel auto-discovers the service provider and registers the `Smstools` facade. Then publish the config file:

```bash
php artisan vendor:publish --tag=smstools-config
```

## Configuration

Add the following to your `.env` file:

```env
SMSTOOLS_CLIENT_ID=your-client-id
SMSTOOLS_CLIENT_SECRET=your-client-secret

# Optional — defaults shown
SMSTOOLS_BASE_URL=https://api.smsgatewayapi.com/v1
SMSTOOLS_TIMEOUT=30
```

Obtain your credentials from the Smstools dashboard under **Advanced → API authentication**.

If `SMSTOOLS_CLIENT_ID` or `SMSTOOLS_CLIENT_SECRET` is not set, a `RuntimeException` is thrown when the client is first resolved from the container.

## Usage

Inject `SmstoolsClient` via the constructor, resolve it from the container, or use the `Smstools` facade.

### Send to a single recipient

```php
use GraystackIT\SmstoolsApi\Facades\Smstools;

$result = Smstools::messages()->send(
    to: '436501234567',
    message: 'Hello from Laravel!',
    sender: 'MyApp',
);

echo $result['messageid']; // h2md1ewkyzjkuyn9ak7pryw1evtyw3x
```

### Send to multiple recipients

```php
$result = Smstools::messages()->send(
    to: ['436501234567', '436501234568'],
    message: 'Hello everyone!',
    sender: 'MyApp',
);

echo count($result['messageids']); // 2
```

### Schedule a message

```php
$result = Smstools::messages()->send(
    to: '436501234567',
    message: 'Your appointment is tomorrow.',
    sender: 'MyApp',
    date: '2026-06-01 09:00',
);
```

### All optional parameters

```php
$result = Smstools::messages()->send(
    to: '436501234567',
    message: 'Test message',
    sender: 'MyApp',
    date: '2026-06-01 10:00',   // Scheduled send time (yyyy-MM-dd HH:mm)
    reference: 'order-999',      // Custom reference string (max 255 chars)
    test: true,                  // Validate without sending — no credits used
    subid: 42,                   // Send from a specific subaccount
);
```

### Using dependency injection

```php
use GraystackIT\SmstoolsApi\SmstoolsClient;

class SmsController extends Controller
{
    public function __construct(private SmstoolsClient $sms) {}

    public function notify(): void
    {
        $this->sms->messages()->send(
            to: '436501234567',
            message: 'Your order has shipped.',
            sender: 'MyShop',
        );
    }
}
```

## Error handling

All API errors throw `GraystackIT\SmstoolsApi\Exceptions\SmstoolsException` (extends `RuntimeException`).

```php
use GraystackIT\SmstoolsApi\Exceptions\SmstoolsException;

try {
    $result = Smstools::messages()->send(
        to: '436501234567',
        message: 'Hello',
        sender: 'MyApp',
    );
} catch (SmstoolsException $e) {
    echo $e->getMessage();       // Human-readable error from the API error code map
    echo $e->getStatusCode();    // HTTP status code (e.g. 400, 401)
    echo $e->getApiErrorCode();  // Smstools API error code (e.g. 104, 108) or null
}
```

### Common API error codes

| Code | Meaning |
|---|---|
| 104 | Invalid credentials |
| 108 | Not enough credits |
| 109 | Sender name cannot be empty |
| 111 | Invalid sender name (max 11 chars or 14 digits) |
| 118 | Message exceeds 612 characters |
| 121 | Scheduled date is invalid or in the past |
| 131 | Number is on the opt-out list |
| 200 | IP address not allowed |

## Testing

Tests use Saloon's `MockClient` — no real API calls are made.

```php
use GraystackIT\SmstoolsApi\Connectors\SmstoolsConnector;
use GraystackIT\SmstoolsApi\Requests\SendMessageRequest;
use GraystackIT\SmstoolsApi\SmstoolsClient;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

$mockClient = new MockClient([
    SendMessageRequest::class => MockResponse::make(
        ['messageid' => 'abc123'],
        200
    ),
]);

$connector = new SmstoolsConnector('client-id', 'client-secret');
$connector->withMockClient($mockClient);

$result = (new SmstoolsClient($connector))->messages()->send(
    to: '436501234567',
    message: 'Hello',
    sender: 'MyApp',
);
```

Run the package test suite:

```bash
vendor/bin/pest
```

## License

MIT.
