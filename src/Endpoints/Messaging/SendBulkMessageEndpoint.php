<?php

namespace Okolaa\TermiiPHP\Endpoints\Messaging;

use Okolaa\TermiiPHP\Data\Message;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class SendBulkMessageEndpoint extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(private readonly Message $message)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/api/sms/send/bulk';
    }

    protected function defaultBody(): array
    {
        return $this->message->toRequestArray();
    }
}
