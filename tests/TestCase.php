<?php

namespace Tests;

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
use Okolaa\TermiiPHP\Endpoints\Insight\GetBalanceEndpoint;
use Okolaa\TermiiPHP\Endpoints\Insight\HistoryEndpoint;
use Okolaa\TermiiPHP\Endpoints\Insight\PhoneNumberStatusEndpoint;
use Okolaa\TermiiPHP\Endpoints\Insight\SearchPhoneNumberEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\GetSenderIdsEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\RequestSenderIdEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\SendBulkMessageEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\SendDeviceTemplateEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\SendMessageEndpoint;
use Okolaa\TermiiPHP\Endpoints\Messaging\SendMessageFromAutoNumberEndpoint;
use Okolaa\TermiiPHP\Endpoints\Token\EmailTokenEndpoint;
use Okolaa\TermiiPHP\Endpoints\Token\InAppTokenEndpoint;
use Okolaa\TermiiPHP\Endpoints\Token\SendTokenEndpoint;
use Okolaa\TermiiPHP\Endpoints\Token\VerifyTokenEndpoint;
use Okolaa\TermiiPHP\Endpoints\Token\VoiceCallEndpoint;
use Okolaa\TermiiPHP\Endpoints\Token\VoiceTokenEndpoint;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

abstract class TestCase extends BaseTestCase
{
    public static function initGlobalMockClient(): void
    {
        MockClient::global([
            '/test'                    => MockResponse::make(),
            SendMessageEndpoint::class => MockResponse::make([
                'message_id' => '9122821270554876574',
                'message'    => 'Successfully Sent',
                'balance'    => 9,
                'user'       => 'Peter Mcleish'
            ]),
            SendBulkMessageEndpoint::class => MockResponse::make([
                'code'       => 'ok',
                'message_id' => '9122821270554876574',
                'message'    => 'Successfully Sent',
                'balance'    => 9,
                'user'       => 'Peter Mcleish'
            ]),
            SendDeviceTemplateEndpoint::class => MockResponse::make([
                'code'       => 'ok',
                'message_id' => '2255298515609943356',
                'message'    => 'Successfully Sent',
                'balance'    => 'unlimited',
                'user'       => 'Termii Inc.'
            ]),
            SendMessageFromAutoNumberEndpoint::class => MockResponse::make([
                'code'       => 'ok',
                'message_id' => '174749423',
                'message'    => 'Successfully Sent',
                'balance'    => 8,
                'user'       => 'Seun Junior'
            ]),
            GetSenderIdsEndpoint::class    => MockResponse::fixture('get_sender_ids'),
            RequestSenderIdEndpoint::class => MockResponse::make(),
            GetPhonebooksEndpoint::class   => MockResponse::fixture('get_phonebooks'),
            CreatePhonebookEndpoint::class => MockResponse::make(),
            UpdatePhonebookEndpoint::class => MockResponse::make(),
            DeletePhonebookEndpoint::class => MockResponse::make(),
            GetContactsEndpoint::class     => MockResponse::fixture('get_contacts'),
            AddContactEndpoint::class      => MockResponse::make([
                'data' => [
                    'id'            => 3647982,
                    'phone_number'  => '123456789',
                    'email_address' => 'test@gmail.com',
                    'first_name'    => 'test',
                    'last_name'     => 'contact',
                    'company'       => 'Termii',
                    'country_code'  => '234',
                    'message'       => null,
                    'create_at'     => '2021-10-11 10:15:35',
                    'updated_at'    => '2021-10-11 10:15:35',
                ]
            ]),
            ImportContactEndpoint::class      => MockResponse::make(),
            DeleteContactEndpoint::class      => MockResponse::make(),
            SendCampaignEndpoint::class       => MockResponse::make(),
            GetCampaignsEndpoint::class       => MockResponse::fixture('get_campaigns'),
            GetCampaignHistoryEndpoint::class => MockResponse::fixture('get_campaign_history'),
            SendTokenEndpoint::class          => MockResponse::make(),
            VoiceTokenEndpoint::class         => MockResponse::make(),
            VerifyTokenEndpoint::class        => MockResponse::make(),
            EmailTokenEndpoint::class         => MockResponse::make(),
            InAppTokenEndpoint::class         => MockResponse::fixture('in_app_token'),
            VoiceCallEndpoint::class          => MockResponse::make(),
            GetBalanceEndpoint::class         => MockResponse::fixture('get_balance'),
            SearchPhoneNumberEndpoint::class  => MockResponse::make([
                'number'       => '2347089509657',
                'status'       => 'DND blacklisted',
                'network'      => 'Airtel Nigeria',
                'network_code' => '62120',
            ]),
            PhoneNumberStatusEndpoint::class => MockResponse::fixture('phone_number_status'),
            HistoryEndpoint::class           => MockResponse::fixture('history'),
        ]);
    }
}
