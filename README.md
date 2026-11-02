# Termii PHP SDK

[![Tests](https://github.com/Douglasokolaa/termii-php/actions/workflows/run-tests.yml/badge.svg)](https://github.com/Douglasokolaa/termii-php/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/Douglasokolaa/termii-php/actions/workflows/phpstan.yml/badge.svg)](https://github.com/Douglasokolaa/termii-php/actions/workflows/phpstan.yml)
[![Latest Version](https://img.shields.io/packagist/v/okolaa/termiiphp)](https://packagist.org/packages/okolaa/termiiphp)
[![License](https://img.shields.io/github/license/Douglasokolaa/termii-php)](LICENSE.md)

Termii REST API PHP library, built on [Saloon](https://docs.saloon.dev/). Send SMS, WhatsApp
and voice messages, manage sender IDs, send and verify OTP tokens, and work with phonebooks,
contacts, campaigns and insights from the [Termii API](https://developers.termii.com/).

## Requirements

- PHP 8.2 or later
- A [Termii](https://termii.com/) account, its API key, and its base URL. Each account has its own
  base URL; find it on your Termii dashboard. It must use `https://`.

## Installation

```bash
composer require okolaa/termiiphp
```

## Quick start

```php
use Okolaa\TermiiPHP\Data\Message;
use Okolaa\TermiiPHP\Enums\MessageChannel;
use Okolaa\TermiiPHP\Termii;

$termii = Termii::initialize('your-api-key', 'https://your-base-url.termii.com');

$response = $termii->messagingApi()->send(new Message(
    to: '2348012345678',
    from: 'YourSender',
    sms: 'Hello from Termii',
    type: 'plain',
    channel: MessageChannel::DND,
));

$response->json(); // ['message_id' => '...', 'message' => 'Successfully Sent', ...]
```

Every method returns a [Saloon response](https://docs.saloon.dev/the-basics/responses).
Failed requests throw an exception; see [Error handling](#error-handling).

`Termii::initialize()` keeps one shared connector and replaces it when called with a different
API key or base URL. If your application serves several Termii accounts, keep one connector per
account: `new \Okolaa\TermiiPHP\TermiiConnector($apiKey, $baseUrl)`.

## Usage

### Messaging

```php
use Okolaa\TermiiPHP\Data\DeviceTemplate;
use Okolaa\TermiiPHP\Data\Message;
use Okolaa\TermiiPHP\Enums\MessageChannel;

// Up to 100 recipients
$termii->messagingApi()->sendBulk(new Message(
    to: ['2348012345678', '2348098765432'],
    from: 'YourSender',
    sms: 'Hello everyone',
    type: 'plain',
    channel: MessageChannel::Generic,
));

// WhatsApp message with media
$termii->messagingApi()->send(new Message(
    to: '2348012345678',
    from: 'your-device-name',
    sms: 'Your receipt',
    type: 'plain',
    channel: MessageChannel::WhatsApp,
    mediaUrl: 'https://example.com/receipt.pdf',
    mediaCaption: 'Receipt',
));

// WhatsApp template
$termii->messagingApi()->sendDeviceTemplate(new DeviceTemplate(
    phoneNumber: '2348012345678',
    templateId: 'your-template-id',
    data: ['product_name' => 'Termii', 'otp' => '120435', 'expiry_time' => '10 minutes'],
    deviceId: 'your-device-id',
));
```

### Sender IDs

```php
use Okolaa\TermiiPHP\Data\SenderId;

$termii->senderIdApi()->getIds(page: 1);
$termii->senderIdApi()->getIds(page: 1, name: 'YourBrand', status: 'active');

$termii->senderIdApi()->requestId(new SenderId(
    id: 'YourBrand',
    company: 'Your Company Ltd',
    useCase: 'Your login code is 123456',
));
```

### Tokens (OTP)

```php
use Okolaa\TermiiPHP\Data\Token\InAppToken;
use Okolaa\TermiiPHP\Data\Token\SendToken;
use Okolaa\TermiiPHP\Data\Token\VoiceToken;
use Okolaa\TermiiPHP\Enums\PinType;
use Okolaa\TermiiPHP\Enums\TokenChannel;

$response = $termii->tokenApi()->send(new SendToken(
    messageType: 'NUMERIC',
    to: '2348012345678',
    from: 'YourSender',
    channel: TokenChannel::DND,
    messageText: 'Your login code is < 1234 >',
    pinPlaceHolder: '< 1234 >',
    pinType: PinType::Numeric,
    pinAttempts: 3,
    pinTimeToLiveMinute: 5,
    pinLength: 6,
));
$pinId = $response->json('pinId');

$termii->tokenApi()->verify($pinId, '123456');

$termii->tokenApi()->voice(new VoiceToken(phoneNumber: '2348012345678'));
$termii->tokenApi()->voiceCall('2348012345678', '123456');
$termii->tokenApi()->email('user@example.com', '123456', 'your-email-configuration-id');
$termii->tokenApi()->inApp(new InAppToken(pinType: PinType::Numeric, phoneNumber: '2348012345678'));
```

### Phonebooks and contacts

```php
use Okolaa\TermiiPHP\Data\Contact;
use Okolaa\TermiiPHP\Data\Phonebook;

$phonebooks = $termii->campaignApi()->phoneBook();

$phonebooks->get(page: 1);
$phonebooks->create(new Phonebook(name: 'Customers', description: 'Paying customers'));
$phonebooks->update(new Phonebook(name: 'Customers', description: 'Updated', id: 'phonebook-id'));
$phonebooks->delete('phonebook-id');

$phonebooks->getContacts('phonebook-id', page: 1);
$phonebooks->addContact('phonebook-id', new Contact(
    phoneNumber: '8012345678',
    email: 'jane@example.com',
    firstName: 'Jane',
    lastName: 'Doe',
    countryCode: '234',
));
$phonebooks->importContact('phonebook-id', '234', fopen('contacts.csv', 'r'));
$phonebooks->deleteContact('contact-id');
```

### Campaigns

```php
use Okolaa\TermiiPHP\Data\Campaign;
use Okolaa\TermiiPHP\Enums\MessageChannel;

$termii->campaignApi()->send(new Campaign(
    phonebookId: 'phonebook-id',
    senderId: 'YourSender',
    channel: MessageChannel::Generic,
    campaignType: 'personalized',
    countryCode: '234',
    message: 'Our sale starts today',
    messageType: 'plain',
    runAt: '2026-12-01 10:00',
    status: 'scheduled',
));

$termii->campaignApi()->get(page: 1);
$termii->campaignApi()->getHistory('campaign-id', page: 1);
```

### Insights

```php
$termii->insightApi()->getBalance();
$termii->insightApi()->searchPhoneNumber('2348012345678');      // DND status
$termii->insightApi()->statusOfPhoneNumber('8012345678', 'NG'); // number status
$termii->insightApi()->history();                                // or ->history('message-id')
```

### Paginated results as objects

List endpoints can map their response to typed objects:

```php
$senderIds = $termii->senderIdApi()->getIds(page: 1)->dto(); // PaginatedData<SenderId>

$senderIds->data;        // SenderId[]
$senderIds->currentPage;
$senderIds->lastPage;
$senderIds->total;
```

### Error handling

Failed requests throw a subclass of `Okolaa\TermiiPHP\RequestException`, so you can catch
everything at once or handle specific cases:

```php
use Okolaa\TermiiPHP\Exceptions\AuthenticationException;
use Okolaa\TermiiPHP\Exceptions\RateLimitException;
use Okolaa\TermiiPHP\Exceptions\ValidationException;
use Okolaa\TermiiPHP\RequestException;

try {
    $termii->insightApi()->getBalance();
} catch (AuthenticationException $e) {      // 401: invalid API key
    // ...
} catch (ValidationException $e) {          // 400 or 422: Termii rejected the data
    $e->getResponse()->json('message');
} catch (RateLimitException $e) {           // 429
    $e->retryAfter();                       // seconds, or null
} catch (RequestException $e) {             // anything else: PermissionException (403),
    $e->getStatus();                        // NotFoundException (404), ServerException (5xx)
}
```

Network failures (DNS, TLS, timeouts) throw `Saloon\Exceptions\Request\FatalRequestException`.
Its message never contains your API key.

### Webhooks

Termii signs each webhook with an `X-Termii-Signature` header. Verify it against the raw
request body before you trust the event:

```php
use Okolaa\TermiiPHP\Webhooks\Events\DeliveryReport;
use Okolaa\TermiiPHP\Webhooks\Events\DeviceStatus;
use Okolaa\TermiiPHP\Webhooks\Events\InboundMessage;
use Okolaa\TermiiPHP\Webhooks\WebhookEvent;
use Okolaa\TermiiPHP\Webhooks\WebhookSignature;

$payload   = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_TERMII_SIGNATURE'] ?? null;

if (! WebhookSignature::isValid($payload, $signature, 'your-termii-secret-key')) {
    http_response_code(401);
    exit;
}

$event = WebhookEvent::fromPayload($payload);

match (true) {
    $event instanceof DeliveryReport => $event->status,   // e.g. "Delivered"
    $event instanceof InboundMessage => $event->message,
    $event instanceof DeviceStatus   => $event->status,   // e.g. "disconnected"
};
```

In Laravel, use `$request->getContent()` and `$request->header('X-Termii-Signature')`.

### Timeouts and debugging

```php
$termii->config()->merge(['connect_timeout' => 60, 'timeout' => 120]);

$termii->debug(); // dumps each request and response
```

`debug()` prints the API key, because Termii requires it in the request.
Do not leave it enabled in production or paste its output publicly.

## What is not covered yet

These Termii endpoints are not implemented yet: WhatsApp template with media, WhatsApp
free-form messages, email product notifications, auto failover and campaign retry. You can still call any endpoint with a custom
[Saloon request](https://docs.saloon.dev/the-basics/requests) sent through `$termii->send()`.

## Security

Please report vulnerabilities privately; see [SECURITY.md](SECURITY.md).

## Contributing

```bash
git clone https://github.com/Douglasokolaa/termii-php.git
cd termii-php
composer install
./vendor/bin/pest
./vendor/bin/phpstan analyze
./vendor/bin/php-cs-fixer check --allow-risky=yes
```

Tests run against recorded fixtures and never call the live API.

## License

MIT. See [LICENSE.md](LICENSE.md).
