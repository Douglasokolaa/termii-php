<?php

use Okolaa\TermiiPHP\Data\Campaign;
use Okolaa\TermiiPHP\Data\CampaignHistory;
use Okolaa\TermiiPHP\Data\Contact;
use Okolaa\TermiiPHP\Data\PaginatedData;
use Okolaa\TermiiPHP\Data\Phonebook;
use Okolaa\TermiiPHP\Endpoints\Campaign\GetCampaignHistoryEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\GetCampaignsEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\AddContactEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\CreatePhonebookEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\DeleteContactEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\DeletePhonebookEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\GetContactsEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\GetPhonebooksEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\ImportContactEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook\UpdatePhonebookEndpoint;
use Okolaa\TermiiPHP\Endpoints\Campaign\SendCampaignEndpoint;
use Okolaa\TermiiPHP\Enums\MessageChannel;

use function Pest\Faker\fake;

use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Traits\Body\HasMultipartBody;
use Saloon\Traits\Request\CreatesDtoFromResponse;

test('it can get phonebooks', closure: function() {
    expect(GetPhonebooksEndpoint::class)
        ->toSendGetRequest()
        ->toUse(CreatesDtoFromResponse::class)
        ->and(
            $response = createTestConnector()
            ->campaignApi()
            ->phoneBook()
            ->get()
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->dto())->toBeInstanceOf(PaginatedData::class)
        ->and($response->dto()->data[0])->toBeInstanceOf(Phonebook::class);
});

test('it can create a phonebook', closure: function() {
    expect(CreatePhonebookEndpoint::class)
        ->toSendPostRequest()
        ->toUse(HasJsonBody::class)
        ->and(
            $response = createTestConnector()
                ->campaignApi()
                ->phoneBook()
                ->create($phonebook = new Phonebook(fake()->jobTitle(), fake()->sentence()))
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getPendingRequest()->body()->all())
        ->toHaveKey('phonebook_name', $phonebook->name)
        ->toHaveKey('description', $phonebook->description);
});

test('it can update a phonebook', closure: function() {
    expect(UpdatePhonebookEndpoint::class)
        ->toSendPatchRequest()
        ->toUse(HasJsonBody::class)
        ->and(
            $response = createTestConnector()
                ->campaignApi()
                ->phoneBook()
                ->update($phonebook = new Phonebook(fake()->jobTitle(), fake()->sentence(), id: (string) fake()->randomNumber()))
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getPendingRequest()->body()->all())
        ->toHaveKey('phonebook_name', $phonebook->name)
        ->toHaveKey('description', $phonebook->description);
});

test('it can delete a phonebook', closure: function() {
    expect(DeletePhonebookEndpoint::class)
        ->toSendDeleteRequest()
        ->and(
            $response = createTestConnector()
                ->campaignApi()
                ->phoneBook()
                ->delete('phonebook-id')
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getRequest()->resolveEndpoint())
        ->toBeString()
        ->toEndWith('/api/phonebooks/phonebook-id');
});

test('it can fetch contacts', closure: function() {
    expect(GetContactsEndpoint::class)
        ->toSendGetRequest()
        ->and(
            $response = createTestConnector()
                    ->campaignApi()
                    ->phoneBook()
                    ->getContacts('phonebook-id')
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getRequest()->resolveEndpoint())
        ->toBeString()
        ->toEndWith('/api/phonebooks/phonebook-id/contacts')
        ->and($response->dto())->toBeInstanceOf(PaginatedData::class)
        ->and($response->dto()->data[0])->toBeInstanceOf(Contact::class);
});

test('it can add a single contact to phonebook', closure: function() {
    expect(AddContactEndpoint::class)
        ->toSendPostRequest()
        ->toUse(HasJsonBody::class)
        ->and(
            $response = createTestConnector()
                ->campaignApi()
                ->phoneBook()
                ->addContact('phonebook-id', new Contact(fake()->phoneNumber(), fake()->safeEmail()))
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getRequest()->resolveEndpoint())
        ->toBeString()
        ->toEndWith('/api/phonebooks/phonebook-id/contacts')
        ->and($response->dto())->toBeInstanceOf(Contact::class);
});

test('it can add a multiple contact to phonebook', closure: function() {
    expect(ImportContactEndpoint::class)
        ->toSendPostRequest()
        ->toUse(HasMultipartBody::class)
        ->and(
            $response = createTestConnector()
                ->campaignApi()
                ->phoneBook()
                ->importContact('phonebook-id', 'NG', fake()->file('./', './vendor'))
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getRequest()->resolveEndpoint())
        ->toBeString()
        ->toEndWith('/api/phonebooks/contacts/upload');
});

test('it can DELETE a contact to phonebook', closure: function() {
    expect(DeleteContactEndpoint::class)
        ->toSendDeleteRequest()
        ->and(
            $response = createTestConnector()
                ->campaignApi()
                ->phoneBook()
                ->deleteContact('contact-id')
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getRequest()->resolveEndpoint())
        ->toBeString()
        ->toEndWith('/api/phonebook/contact/contact-id');
});

test('it can send campaign', closure: function() {
    expect(SendCampaignEndpoint::class)
        ->toSendPostRequest()
        ->toUse(HasJsonBody::class)
        ->and(
            $response = createTestConnector()
                ->campaignApi()
                ->send(new Campaign(fake()->phoneNumber(), fake()->slug(), MessageChannel::Generic, 'personalized'))
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getRequest()->resolveEndpoint())
        ->toBeString()
        ->toEndWith('/api/sms/campaigns/send');
});

test('it can fetch campaigns', closure: function() {
    expect(GetCampaignsEndpoint::class)
        ->toSendGetRequest()
        ->and(
            $response = createTestConnector()
                ->campaignApi()
                ->get()
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getRequest()->resolveEndpoint())
        ->toBeString()
        ->toEndWith('/api/sms/campaigns')
        ->and($response->dto())->toBeInstanceOf(PaginatedData::class)
        ->and($response->dto()->data[0])->toBeInstanceOf(Campaign::class);
    ;
});

test('it can fetch campaign HISTORY', closure: function() {
    expect(GetCampaignHistoryEndpoint::class)
        ->toSendGetRequest()
        ->and(
            $response = createTestConnector()
                ->campaignApi()
                ->getHistory(64)
        )
        ->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->getRequest()->resolveEndpoint())
        ->toBeString()
        ->toEndWith('/api/sms/campaigns/64')
        ->and($response->dto())->toBeInstanceOf(PaginatedData::class)
        ->and($response->dto()->data[0])->toBeInstanceOf(CampaignHistory::class);
});
