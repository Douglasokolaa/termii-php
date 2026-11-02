<?php

namespace Okolaa\TermiiPHP\Endpoints\Campaign\Phonebook;

use InvalidArgumentException;
use Okolaa\TermiiPHP\Data\Phonebook;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdatePhonebookEndpoint extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function resolveEndpoint(): string
    {
        return '/api/phonebooks/' . rawurlencode((string) $this->phonebook->id);
    }

    public function __construct(private readonly Phonebook $phonebook)
    {
        if ($phonebook->id === null || $phonebook->id === '') {
            throw new InvalidArgumentException('A phonebook id is required to update a phonebook.');
        }
    }

    protected function defaultBody(): array
    {
        return $this->phonebook->toRequestArray();
    }
}
