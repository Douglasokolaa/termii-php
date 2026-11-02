<?php

use Okolaa\TermiiPHP\Data\Message;
use Okolaa\TermiiPHP\Data\PaginatedData;
use Okolaa\TermiiPHP\Data\SenderId;
use Okolaa\TermiiPHP\Endpoints\Insight\GetBalanceEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\GetSenderIdsEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\RequestSenderIdEndpoint;
use Okolaa\TermiiPHP\Enums\MessageChannel;
use Okolaa\TermiiPHP\Exceptions\AuthenticationException;
use Okolaa\TermiiPHP\Exceptions\NotFoundException;
use Okolaa\TermiiPHP\Exceptions\PermissionException;
use Okolaa\TermiiPHP\Exceptions\RateLimitException;
use Okolaa\TermiiPHP\Exceptions\ServerException;
use Okolaa\TermiiPHP\Exceptions\ValidationException;
use Okolaa\TermiiPHP\RequestException;
use Okolaa\TermiiPHP\TermiiConnector;
use Okolaa\TermiiPHP\Webhooks\Events\DeliveryReport;
use Okolaa\TermiiPHP\Webhooks\Events\DeviceStatus;
use Okolaa\TermiiPHP\Webhooks\Events\InboundMessage;
use Okolaa\TermiiPHP\Webhooks\WebhookEvent;
use Okolaa\TermiiPHP\Webhooks\WebhookSignature;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

// Sender IDs

test('sender id requests use the documented path and field', function() {
    $request = new RequestSenderIdEndpoint(new SenderId('Acme', 'Acme Ltd', 'Login codes'));

    expect($request->resolveEndpoint())->toBe('/api/sender-id/request')
        ->and($request->body()->all())->toHaveKey('use_case', 'Login codes')->not->toHaveKey('usecase');
});

test('sender id list sends name and status filters only when given', function() {
    expect((new GetSenderIdsEndpoint(1, 'Acme', 'active'))->query()->all())
        ->toMatchArray(['page' => 1, 'name' => 'Acme', 'status' => 'active'])
        ->and(array_filter((new GetSenderIdsEndpoint(1))->query()->all(), fn ($v) => $v !== null))
        ->toBe(['page' => 1]);
});

test('paginated data reads the documented content page shape', function() {
    $connector = new TermiiConnector('key', 'https://v3.api.termii.com');
    $connector->withMockClient(new MockClient([
        '*' => MockResponse::make([
            'content' => [
                ['country' => 'Nigeria', 'status' => 'active', 'sender_id' => 'Acme'],
                ['company' => 'Beta', 'status' => 'pending', 'sender_id' => 'Beta', 'usecase' => 'OTP'],
            ],
            'pageable'      => ['offset' => 15],
            'totalElements' => 17,
            'totalPages'    => 2,
            'size'          => 15,
            'number'        => 1,
        ]),
    ]));

    $page = $connector->senderIdApi()->getIds(2)->dto();

    expect($page)->toBeInstanceOf(PaginatedData::class)
        ->and($page->data)->toHaveCount(2)
        ->and($page->data[1]->useCase)->toBe('OTP')
        ->and($page->data[0]->company)->toBeNull()
        ->and([$page->currentPage, $page->lastPage, $page->total, $page->perPage, $page->from, $page->to])
        ->toBe([2, 2, 17, 15, 16, 17]);
});

// Messaging

test('media is only sent when a media url is set', function() {
    expect((new Message('234800', 'Acme', 'Hi'))->toRequestArray())->not->toHaveKey('media')
        ->and((new Message('234800', 'Acme', 'Hi', mediaUrl: 'https://x.test/a.pdf', mediaCaption: 'A'))->toRequestArray())
        ->toHaveKey('media', ['url' => 'https://x.test/a.pdf', 'caption' => 'A']);
});

test('voice is a supported message channel', function() {
    expect(MessageChannel::from('voice'))->toBe(MessageChannel::Voice);
});

// Webhooks

test('webhook signature accepts a valid hmac-sha512 and rejects anything else', function() {
    $payload   = '{"type":"device_status","status":"disconnected"}';
    $signature = hash_hmac('sha512', $payload, 'secret');

    expect(WebhookSignature::isValid($payload, $signature, 'secret'))->toBeTrue()
        ->and(WebhookSignature::isValid($payload, strtoupper($signature), 'secret'))->toBeTrue()
        ->and(WebhookSignature::isValid($payload . ' ', $signature, 'secret'))->toBeFalse()
        ->and(WebhookSignature::isValid($payload, $signature, 'other'))->toBeFalse()
        ->and(WebhookSignature::isValid($payload, null, 'secret'))->toBeFalse()
        ->and(WebhookSignature::isValid($payload, '', 'secret'))->toBeFalse()
        ->and(WebhookSignature::isValid($payload, hash_hmac('sha512', $payload, ''), ''))->toBeFalse();
});

test('webhook events parse into typed objects', function(string $payload, string $class) {
    expect(WebhookEvent::fromPayload($payload))->toBeInstanceOf($class);
})->with([
    'delivery report' => ['{"type":"outbound","message_id":"1","receiver":"234800","status":"Delivered","sent_at":"2026-01-01 10:00:00","cost":1.5}', DeliveryReport::class],
    'inbound message' => ['{"type":"inbound","message_id":"2","sender":"234800","message":"Hi","received_at":"2026-01-01 10:00:00"}', InboundMessage::class],
    'device status'   => ['{"type":"device_status","status":"disconnected","device_id":"d1","name":"Main"}', DeviceStatus::class],
]);

test('webhook event fields are mapped', function() {
    $report = WebhookEvent::fromPayload('{"message_id":"9","status":"Delivered","cost":2,"channel":"dnd"}');

    expect($report)->toBeInstanceOf(DeliveryReport::class)
        ->and([$report->messageId, $report->status, $report->cost, $report->channel])->toBe(['9', 'Delivered', '2', 'dnd']);
});

test('malformed webhook payloads are rejected', function(string $payload) {
    WebhookEvent::fromPayload($payload);
})->with(['not json', '"a string"', ''])->throws(InvalidArgumentException::class);

// Exceptions

test('failed responses throw a typed exception that is still a RequestException', function(int $status, string $class) {
    $connector = new TermiiConnector('key', 'https://v3.api.termii.com');
    $connector->withMockClient(new MockClient([GetBalanceEndpoint::class => MockResponse::make(['message' => 'x'], $status)]));

    try {
        $connector->insightApi()->getBalance();
        $this->fail('Expected exception');
    } catch (RequestException $e) {
        expect($e)->toBeInstanceOf($class)->and($e->getStatus())->toBe($status);
    }
})->with([
    [400, ValidationException::class],
    [401, AuthenticationException::class],
    [403, PermissionException::class],
    [404, NotFoundException::class],
    [422, ValidationException::class],
    [429, RateLimitException::class],
    [500, ServerException::class],
    [503, ServerException::class],
    [418, RequestException::class],
]);

test('rate limit exception exposes retry-after seconds', function() {
    $connector = new TermiiConnector('key', 'https://v3.api.termii.com');
    $connector->withMockClient(new MockClient([GetBalanceEndpoint::class => MockResponse::make([], 429, ['Retry-After' => '30'])]));

    try {
        $connector->insightApi()->getBalance();
        $this->fail('Expected exception');
    } catch (RateLimitException $e) {
        expect($e->retryAfter())->toBe(30);
    }
});
