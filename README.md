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

```php
use Okolaa\TermiiPHP\Data\Message;
use Okolaa\TermiiPHP\Termii;

$termii = Termii::initialize('api-token'));

// send Message
$response = $termii->messagingApi()->send(
        new Message(
            to: "23490555546",
            from: "talert",
            sms: "Hi There, Testing Termii",
            type: "plain"
        )
    );

// send Bulk message
$response = $termii->messagingApi()->sendBulk(new Message(...));

// send Device Template
$response = $termii->messagingApi()->sendDeviceTemplate(new \Okolaa\TermiiPHP\Data\DeviceTemplate(...));

// Sender ID
$response = $termii->senderIdApi()->getIds($pageNumber);
$response = $termii->senderIdApi()->requestId(new \Okolaa\TermiiPHP\Data\SenderId(...));

// Campaigns
$response = $termii->campaignApi()->send(new \Okolaa\TermiiPHP\Data\Campaign(...));
$response = $termii->campaignApi()->get($campaingId, $pageNumber);
$response = $termii->campaignApi()->getHistory($pageNumber);

// Phonebook
$phonebook = new \Okolaa\TermiiPHP\Data\Phonebook(...);
$response = $termii->campaignApi()->phoneBook()->create($phonebook);
$response = $termii->campaignApi()->phoneBook()->update($phonebook);
$response = $termii->campaignApi()->phoneBook()->get();
$response = $termii->campaignApi()->phoneBook()->delete($phonebook->id);

// Contact
$contact = new \Okolaa\TermiiPHP\Data\Contact(...);
$response = $termii->campaignApi()->phoneBook()->addContact($contact);
$response = $termii->campaignApi()->phoneBook()->importContact($phonebook->id, 234, 'path/to/your/file.csv');
$response = $termii->campaignApi()->phoneBook()->getContacts($phonebook->id, $pageNumber);
$response = $termii->campaignApi()->phoneBook()->deleteContact($contact->id);



```

#### Token

Token allows businesses to generate, send, and verify one-time-passwords.

```php

```

## Advanced Configuration

- Customizing Requests

```php
use Okolaa\TermiiPHP\Endpoints\Messaging\RequestSenderIdEndpoint;
use Okolaa\TermiiPHP\Termii;

$request = new RequestSenderIdEndpoint(
    new \Okolaa\TermiiPHP\Data\SenderId(
        'okolaa',
        'Okolaa INC',
        'To be used for sending alerts to customers.'
    )
);
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
$termii = Termii::initialize('api-token'));
$response = $termii->send($request);

$response->json(); // returns array/scalar value
$response->collect(); // returns Illuminate/Collection or scalar value
$response->object(); // returns php object
$response->dto(); // returns Data objects e.g. PaginationData, SenderId, Message, Phonebook
$response->headers(); // returns all the response headers
$response->stream(); // returns the body as a stream
```

-- Handling Timeouts
The SDK allows you to configure timeouts for HTTP requests:

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
