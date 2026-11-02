<?php

namespace Okolaa\TermiiPHP\Endpoints\Messaging;

use Okolaa\TermiiPHP\Data\SenderId;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class RequestSenderIdEndpoint extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(private readonly SenderId $sender)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/api/sender-id/request';
    }

    protected function defaultBody(): array
    {
        return $this->sender->toRequestArray();
    }
}
