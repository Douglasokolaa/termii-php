<?php

namespace Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeletePhonebookEndpoint extends Request
{
    protected Method $method = Method::DELETE;

    public function resolveEndpoint(): string
    {
        return '/api/phonebooks/' . rawurlencode($this->phonebookId);
    }

    public function __construct(private readonly string $phonebookId)
    {
    }
}
