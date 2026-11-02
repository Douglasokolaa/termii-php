<?php

namespace Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteContactEndpoint extends Request
{
    protected Method $method = Method::DELETE;

    public function resolveEndpoint(): string
    {
        return '/api/phonebook/contact/' . rawurlencode($this->contactId);
    }

    public function __construct(private readonly string $contactId)
    {
    }
}
