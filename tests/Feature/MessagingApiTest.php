<?php

use Okolaa\TermiiPHP\Data\DeviceTemplate;
use Okolaa\TermiiPHP\Data\Message;
use Okolaa\TermiiPHP\Endpoints\Messaging\SendBulkMessageEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\SendMessageEndpoint;

use function Pest\Faker\fake;

use Saloon\Traits\Body\HasJsonBody;

test('it can send message', closure: function() {
    expect(SendMessageEndpoint::class)
        ->toSendPostRequest()
        ->toUse(HasJsonBody::class)
        ->and(
            $response = createTestConnector()
                ->messagingApi()
                ->send($message = new Message(fake()->phoneNumber(), fake()->company(), fake()->sentence()))
        )
        ->toBeInstanceOf(\Saloon\Http\Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getPendingRequest()->body()->all())
        ->toHaveKey('to', $message->to)
        ->toHaveKey('from', $message->from)
        ->toHaveKey('sms', $message->sms);
});

test('it can send bulk message', closure: function() {
    expect(SendBulkMessageEndpoint::class)
        ->toSendPostRequest()
        ->toUse(HasJsonBody::class)
        ->and(
            $response = createTestConnector()
                ->messagingApi()
                ->sendBulk($message = new Message([fake()->phoneNumber()], fake()->company(), fake()->sentence()))
        )
        ->toBeInstanceOf(\Saloon\Http\Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getPendingRequest()->body()->all())
        ->toHaveKey('to', $message->to)
        ->toHaveKey('from', $message->from)
        ->toHaveKey('sms', $message->sms);
});

test('it can send device template', closure: function() {
    expect(\Okolaa\TermiiPHP\Endpoints\Messaging\SendDeviceTemplateEndpoint::class)
        ->toSendPostRequest()
        ->toUse(HasJsonBody::class)
        ->and(
            createTestConnector()
                ->messagingApi()
                ->sendDeviceTemplate(new DeviceTemplate(fake()->phoneNumber(), fake()->uuid(), []))
                ->status()
        )
        ->toBe(200);
});

test('it can send message with termii number', closure: function() {
    expect(\Okolaa\TermiiPHP\Endpoints\Messaging\SendMessageFromAutoNumberEndpoint::class)
        ->toSendPostRequest()
        ->toUse(HasJsonBody::class)
        ->and(
            createTestConnector()
                ->messagingApi()
                ->SendMessageFromAutoNumber(fake()->phoneNumber(), fake()->sentence())
                ->status()
        )
        ->toBe(200);
});

test('it can get sender ids', closure: function() {
    expect(\Okolaa\TermiiPHP\Endpoints\Messaging\GetSenderIdsEndpoint::class)
        ->toSendGetRequest()
        ->and(
            createTestConnector()
                ->senderIdApi()
                ->getIds(2)
                ->dto()
        )
        ->toBeInstanceOf(\Okolaa\TermiiPHP\Data\PaginatedData::class);
});

test('it can request sender id', closure: function() {
    expect(\Okolaa\TermiiPHP\Endpoints\Messaging\RequestSenderIdEndpoint::class)
        ->toSendPostRequest()
        ->and(
            $response = createTestConnector()
                ->senderIdApi()
                ->requestId(new \Okolaa\TermiiPHP\Data\SenderId('demo-inc', 'Demo Inc', 'send notifications'))
        )
        ->toBeInstanceOf(\Saloon\Http\Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getPendingRequest()->body()->all())
        ->toHaveKey('company', 'Demo Inc')
        ->toHaveKey('use_case', 'send notifications')
        ->toHaveKey('sender_id', 'demo-inc');
});
