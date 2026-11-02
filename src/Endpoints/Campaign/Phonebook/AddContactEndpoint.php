<?php

namespace Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook;

use Okolaa\TermiiPHP\Data\Contact;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class AddContactEndpoint extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(private readonly string $phonebookId, private readonly Contact $contact)
    {
    }

    protected function defaultBody(): array
    {
        return $this->contact->toRequestArray();
    }

    public function resolveEndpoint(): string
    {
        return '/api/phonebooks/' . rawurlencode($this->phonebookId) . '/contacts';
    }

    public function createDtoFromResponse(Response $response): Contact
    {
        return Contact::fromArray($response->json('data'));
    }
}
