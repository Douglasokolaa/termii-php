<h1 align="center">Termii PHP SDK</h1>

<p align="center">
  <img alt="Github top language" src="https://img.shields.io/github/languages/top/Douglasokolaa/termiiphp?color=56BEB8">

  <img alt="License" src="https://img.shields.io/github/license/Douglasokolaa/termiiphp?color=56BEB8">

  <img alt="Github issues" src="https://img.shields.io/github/issues/Douglasokolaa/termiiphp?color=56BEB8" />

  <img alt="Github forks" src="https://img.shields.io/github/forks/Douglasokolaa/termiiphp?color=56BEB8" />

  <!-- <img alt="Github stars" src="https://img.shields.io/github/stars/Douglasokolaa/termiiphp?color=56BEB8" /> -->
</p>

<hr>

<p align="center">
  <a href="#dart-about">About</a> &#xa0; | &#xa0; 
  <a href="#white_check_mark-requirements">Requirements</a> &#xa0; | &#xa0;
  <a href="#checkered_flag-usage">Usage</a> &#xa0; | &#xa0;
  <a href="#hammer-contribution">Contribution</a> &#xa0; | &#xa0;
  <a href="#memo-license">License</a> &#xa0; | &#xa0;
  <a href="https://github.com/Douglasokolaa" target="_blank">Author</a>
</p>

<br>

## :dart: About ##

Termii PHP SDK is a robust library designed to facilitate seamless integration with
the [Termii API](http://developer.termii.com/docs/).
It enables developers to efficiently send SMS messages, manage sender IDs,
handle campaigns, and verify tokens within PHP applications.

## :white_check_mark: Requirements ##

1. To use Termii's APIs, you need to first create an account for free at [termii.com](https://termii.com/).
2. BASE URL: Your Termii account has its own base URL, which you should use in all API requests.
   Your base URL can be found on your dashboard.

3. Ensure that PHP 8.1 [PHP 8.1+](https://php.net/) or higher is installed on your system.

## :hammer: Installation ##

```bash
# Installation
composer require okolaa/termiiphp
```

## :checkered_flag: Usage ##

### TLDR;

> * Your API Token can be found in your Termii account settings https://app.termii.com/account/api

```php
    
    use Okolaa\TermiiPHP\Endpoints\Messaging\GetSenderIdsEndpoint;
    use Okolaa\TermiiPHP\Termii;
    
    // Initialize the SDK
    $termii = Termii::initialize('api-token', 'https://termi-base-url');
    
    // make a request
    $response = $termii->senderIdApi()->getIds(page: 1);
    
    // get result as array
    $response->json();
    
    // Alternatively, convert result to DTO
    $request = new GetSenderIdsEndpoint();
    $senderIds = $request->createDtoFromResponse($response);
    // you can now interact with data e.g.
    $senderIds->currentPage; // int
    $senderIds->currentPage; // int
    $senderIds->lastPage; // int
    $senderIds->total; //int
    $senderIds->data; //array 

```

### Switch API

Termii’s Messaging allows you to send messages to any country in the world across SMS and WhatsApp channel through a
REST API. Every request made is identified by a unique ID that help our users track the status of their message either
by receiving Delivery Reports (DLRs) over their set webhook endpoints or polling the status of the message using a
specific endpoint.
#### Messaging

### Send Message

```php
use Okolaa\TermiiPHP\Data\Message;
use Okolaa\TermiiPHP\Termii;

$termii = Termii::initialize('api-token');
$message = new Message(
    to: "23490555546",
    from: "talert",
    sms: "Hi There, Testing Termii",
    type: "plain",
    channel:\Okolaa\TermiiPHP\Enums\MessageChannel::DND,
    media: null,    
    time_to_live: 0 
);
$response = $termii->messagingApi()->send($message);
```

### Send Bulk Message

```php
$bulkMessage = new Message(
    to: ["2347012345678", "2349067890123"],
    from: "bulkAlert",
    sms: "Bulk message content, Testing Termii",
    type: "plain",
    channel: \Okolaa\TermiiPHP\Enums\MessageChannel::DND, 
    media: null,    
    time_to_live: 1 // Example TTL in minutes
);
$response = $termii->messagingApi()->sendBulk($bulkMessage);
```

### Send Device Template

```php
$deviceTemplate = new \Okolaa\TermiiPHP\Data\DeviceTemplate(
    template_id: "sampleTemplate1234",
    phone_number: "2348076543210",
    device_key: "ABCD1234DEVICEKEY"
);
$response = $termii->messagingApi()->sendDeviceTemplate($deviceTemplate);
```

### Sender ID

```php
$response = $termii->senderIdApi()->getIds($pageNumber);
$senderId = new \Okolaa\TermiiPHP\Data\SenderId(...);
$senderId = new \Okolaa\TermiiPHP\Data\SenderId(
    name: "TestSender",
    company: "TestCompany Ltd",
    purpose: "Testing purposes for SDK"
);
```

### Campaigns

```php
$campaign = new \Okolaa\TermiiPHP\Data\Campaign(
    name: "Test Campaign",
    message: "This is a test campaign message",
    sender_id: "TestSenderId",
    recipients: ["2347012345678", "2349067890123"],
    channel: "sms", 
    time_to_send: "2023-12-01 10:00:00"
);
$response = $termii->campaignApi()->send($campaign);
$response = $termii->campaignApi()->get($campaingId, $pageNumber);
$response = $termii->campaignApi()->getHistory($pageNumber);
```

### Phonebook

```php
$phonebook = new \Okolaa\TermiiPHP\Data\Phonebook(
    name: "Test Phonebook",
    description: "Sample description for test phonebook"
);
$responseCreate = $termii->campaignApi()->phoneBook()->create($phonebook);
$responseUpdate = $termii->campaignApi()->phoneBook()->update($phonebook);
$response = $termii->campaignApi()->phoneBook()->get();
$response = $termii->campaignApi()->phoneBook()->delete($phonebook->id);
```

### Contact

```php
$contact = new \Okolaa\TermiiPHP\Data\Contact(
    phone_number: "2347012345678",
    first_name: "John",
    last_name: "Doe",
    email: "johndoe@example.com",
    phonebook_id: "testPhonebook123"
);
$responseAddContact = $termii->campaignApi()->phoneBook()->addContact($contact);
$importedContactsResponse = $termii->campaignApi()->phoneBook()->importContact($phonebook->id, 234, 'path/to/your/file.csv');

$fetchedContactsResponse = $termii->campaignApi()->phoneBook()->getContacts($phonebook->id, $pageNumber);
$deleteContactResponse = $termii->campaignApi()->phoneBook()->deleteContact($contact->id);
```

## Token API

The `TokenApi` in the Termii PHP SDK enables businesses to **generate**, **send**, and **verify One-Time Passwords (
OTPs)**.
It's a powerful feature tailored for two-factor authentication and secure user verification processes.

### Send Token

```php
use Okolaa\TermiiPHP\Data\TokenSend;
use Okolaa\TermiiPHP\Termii;

$termii = Termii::initialize('api-token');

$tokenSend = new \Okolaa\TermiiPHP\Data\Token\SendToken(
    to: "23490555546",
    from: "talert",
    messageText: "Your verification code is <%pin%>",
    pinType: \Okolaa\TermiiPHP\Enums\PinType::AlphaNumeric,
    pinAttempts: 3,
    pinTimeToLiveMinute: 5,
    pinLength: 6,
    pinPlaceHolder: "<%pin%>",
    channel: \Okolaa\TermiiPHP\Enums\TokenChannel::DND,
    messageType: 'plain',
);

$response = $termii->tokenApi()->send($tokenSend);
```

### Voice Token

The voice token API enables you to generate and trigger one-time passwords (OTP) through the voice channel to a phone
number.

```php
use Okolaa\TermiiPHP\Data\Token\VoiceToken;
use Okolaa\TermiiPHP\Termii;

$termii = Termii::initialize('api-token');

$voiceToken = new VoiceToken(
    phoneNumber: "2348000000000",
    pin_length: 6
);

$response = $termii->tokenApi()->voice($voiceToken);
```

### Voice Call Token

The voice call API enables you to send messages from your application through our voice channel to a phone number.

```php
use Okolaa\TermiiPHP\Data\Token\VoiceToken;
use Okolaa\TermiiPHP\Termii;

$termii = Termii::initialize('api-token');

$response = $termii->tokenApi()->voiceCall('2348000000000', '12345);
```

### Verify Token

Verify token API, checks tokens sent to customers and returns a response confirming the status of the token.

```php
use Okolaa\TermiiPHP\Termii;

$termii = Termii::initialize('api-token');

$response = $termii->tokenApi()->verify('pinId', 'the-pin-code');
```

### Email Token

The email token API enables you to send one-time-passwords from your application through our email channel to an email
address.

```php
use Okolaa\TermiiPHP\Termii;

$termii = Termii::initialize('api-token');

$response = $termii->tokenApi()->email('test@test.com', 'the-pin-code', 'email-config-id');
```

### In-App Token

This API returns OTP codes in JSON format which can be used within any web or mobile app.

```php
use Okolaa\TermiiPHP\Termii;

$termii = Termii::initialize('api-token');

$payload = new \Okolaa\TermiiPHP\Data\Token\InAppToken(
            pinType: \Okolaa\TermiiPHP\Enums\PinType::Numeric,
            phoneNumber: '2348000000000'
         );

$response = $termii->tokenApi()->inApp($payload);
```

## Advanced Configuration

- Customizing Requests

```php
use Okolaa\TermiiPHP\Endpoints\Messaging\RequestSenderIdEndpoint;
use Okolaa\TermiiPHP\Termii;

$senderIdData = new \Okolaa\TermiiPHP\Data\SenderId(
    'okolaa',
    'Okolaa INC',
    'To be used for sending alerts to customers.'
);
$request = new RequestSenderIdEndpoint($senderIdData);
$request->query()->merge(['page' => 4]);
$request->headers()->merge(...);
$request->body()->merge(...);
$request->config()->merge(...);

$client = Termii::initialize('api-token');
$response = $client->send($request);

```

- The Response Class

```php
use Okolaa\TermiiPHP\Termii;
$termii = Termii::initialize('api-token');
$response = $termii->send($request);

$response->json(); // returns array/scalar value
$response->collect(); // returns Illuminate/Collection or scalar value
$response->object(); // returns php object
$response->dto(); // returns Data objects e.g. PaginationData, SenderId, Message, Phonebook
$response->headers(); // returns all the response headers
$response->stream(); // returns the body as a stream
```

## Troubleshooting
The simplest way to debug a request and response is to use the debug()
method on your connector before sending a request.
This will output an easy-to-understand array of the request and the response.
```php
    use Okolaa\TermiiPHP\Termii;
    $termii = Termii::initialize('api-token'));
    // You can provide the die argument if you would like to terminate the application after receiving the response.
    $termii->debug(die: false);
```
You may also use debugRequest and debugResponse independently
if you would like to debug just the request or response respectively.
   ```php
      $termii->debugRequest(); // $connector->debugRequest(die: true);
      $termii->debugResponse(); // $connector->debugResponse(die: true);
   ```

### Common Issues
1. Invalid API Key:
   Ensure you’ve set a valid API key. You can validate this by logging into the Termii Dashboard.
   
2. SSL Certificate Issues:
    If you encounter SSL errors in local environments, ensure your system’s CA certificates are up to date.
    Alternatively, you can disable the SSL certificate verification as shown below:
    ```php
        $termii->config()
            ->merge([
               'verify' => false
            ]);
    ```
3. Timeouts:
   Increase the timeout values if the Termii API is slow to respond:
   ```php
      use Okolaa\TermiiPHP\Termii;
      $termii = Termii::initialize('api-token'));
      $termii->config()->merge(
       [
           'connect_timeout' => 60,
           'timeout' => 120
       ]
      );
   ```

## :hammer: Contribution

```shell
# fork and Clone the fork project
# Access the folder
cd termiiphp

# Install dependencies
composer Install

# Run test
./vendor/bin/pest
```

## :memo: License ##

This project is under license from MIT. For more details, see the [LICENSE](LICENSE.md) file.

Made with :heart: by <a href="https://github.com/Douglasokolaa" target="_blank">Douglas Okolaa</a>

&#xa0;


<a href="#top">Back to top</a>
