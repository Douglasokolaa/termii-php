<?php

namespace Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook;

use Okolaa\TermiiPHP\Data\Contact;
use Okolaa\TermiiPHP\Data\PaginatedData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Request\CreatesDtoFromResponse;

class GetContactsEndpoint extends Request
{
    use CreatesDtoFromResponse;

    protected Method $method = Method::GET;

    public function __construct(private readonly string $phonebookId, private readonly int $page)
    {
    }

    protected function defaultQuery(): array
    {
        return [
            'page' => $this->page,
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/api/phonebooks/' . rawurlencode($this->phonebookId) . '/contacts';
    }

    /**
     * @param  Response               $response
     * @return PaginatedData<Contact>
     */
    public function createDtoFromResponse(Response $response): PaginatedData
    {
        return PaginatedData::fromResponse($response, Contact::class);
    }
}
