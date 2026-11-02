<?php

use Okolaa\TermiiPHP\Data\Contact;
use Okolaa\TermiiPHP\Data\Message;
use Okolaa\TermiiPHP\Data\Phonebook;
use Okolaa\TermiiPHP\Endpoints\Campaign\GetCampaignHistoryEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\DeleteContactEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\DeletePhonebookEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\GetContactsEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\UpdatePhonebookEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\SendBulkMessageEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\SendMessageEndpoint;
use Okolaa\TermiiPHP\Termii;
use Okolaa\TermiiPHP\TermiiAuthenticator;
use Okolaa\TermiiPHP\TermiiConnector;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;

test('initialize never returns a connector built for a different api key', function() {
    $first  = Termii::initialize('key-one');
    $second = Termii::initialize('key-two');

    expect($second)->not->toBe($first)
        ->and(Termii::initialize('key-two'))->toBe($second)
        ->and(Termii::initialize('key-two', 'https://other.example'))->not->toBe($second);
});

test('rejects non-https base urls', function(string $url) {
    new TermiiConnector('key', $url);
})->with(['http://v3.api.termii.com', 'v3.api.termii.com', 'ftp://example.com'])
    ->throws(InvalidArgumentException::class);

test('rejects an empty api key', function() {
    new TermiiConnector('', 'https://v3.api.termii.com');
})->throws(InvalidArgumentException::class);

test('api key is hidden from var_dump and print_r', function() {
    $connector = new TermiiConnector('super-secret-key', 'https://v3.api.termii.com');

    expect(print_r($connector, true))->not->toContain('super-secret-key')
        ->and(print_r(new TermiiAuthenticator('super-secret-key'), true))->not->toContain('super-secret-key');
});

test('transport errors do not leak the api key', function() {
    $connector = new TermiiConnector('super-secret-key', 'https://v3.api.termii.com');
    $connector->withMockClient(new MockClient([
        '*' => function(PendingRequest $pendingRequest) {
            throw new FatalRequestException(
                new RuntimeException('cURL error 28: timed out for https://v3.api.termii.com/api/get-balance?api_key=super-secret-key'),
                $pendingRequest
            );
        },
    ]));

    try {
        $connector->insightApi()->getBalance();
        $this->fail('Expected exception');
    } catch (FatalRequestException $e) {
        $chain = '';
        for ($t = $e; $t !== null; $t = $t->getPrevious()) {
            $chain .= $t->getMessage();
        }

        expect($chain)->not->toContain('super-secret-key')->toContain('api_key=[redacted]');
    }
});

test('path parameters are url encoded', function() {
    expect((new DeleteContactEndpoint('../sender-id?x=1'))->resolveEndpoint())
        ->toBe('/api/phonebook/contact/..%2Fsender-id%3Fx%3D1')
        ->and((new DeletePhonebookEndpoint('a/b'))->resolveEndpoint())->toBe('/api/phonebooks/a%2Fb')
        ->and((new GetContactsEndpoint('a/b', 1))->resolveEndpoint())->toBe('/api/phonebooks/a%2Fb/contacts')
        ->and((new GetCampaignHistoryEndpoint('a/b', 1))->resolveEndpoint())->toBe('/api/sms/campaigns/a%2Fb');
});

test('updating a phonebook requires an id', function() {
    new UpdatePhonebookEndpoint(new Phonebook('name', 'description'));
})->throws(InvalidArgumentException::class);

test('single and bulk sends hit the documented endpoints', function() {
    $message = new Message('2348000000000', 'Sender', 'Hi');

    expect((new SendMessageEndpoint($message))->resolveEndpoint())->toBe('/api/sms/send')
        ->and((new SendBulkMessageEndpoint($message))->resolveEndpoint())->toBe('/api/sms/send/bulk');
});

test('contact payload sends phone_number', function() {
    expect((new Contact('2348000000000'))->toRequestArray())
        ->toHaveKey('phone_number', '2348000000000')
        ->not->toHaveKey('phonebook_name');
});
