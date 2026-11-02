<?php

namespace Okolaa\TermiiPHP;

use Saloon\Contracts\Authenticator;
use Saloon\Contracts\Body\BodyRepository;
use Saloon\Contracts\Body\MergeableBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\PendingRequest;
use Saloon\Repositories\Body\MultipartBodyRepository;

class TermiiAuthenticator implements Authenticator
{
    public function __construct(#[\SensitiveParameter] protected readonly string $apiKey)
    {
    }

    /**
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return ['apiKey' => '[redacted]'];
    }

    /**
     * @inheritDoc
     */
    public function set(PendingRequest $pendingRequest): void
    {
        if ($this->isQueryBasedMethod($pendingRequest->getMethod())) {
            $pendingRequest->query()->add('api_key', $this->apiKey);
        } else {
            $this->mergeApiKeyIntoBody($pendingRequest->body());
        }
    }

    /**
     * Check if the HTTP method supports query-based API key injection.
     */
    private function isQueryBasedMethod(Method $method): bool
    {
        return match ($method) {
            Method::GET, Method::DELETE => true,
            default                     => false,
        };
    }

    /**
     * Merge the API key into the request body if the body is mergeable.
     */
    private function mergeApiKeyIntoBody(BodyRepository $body): void
    {
        if ($body instanceof MultipartBodyRepository) {
            $contact = $body->get('contact');
            $fields  = $contact instanceof MultipartValue && is_string($contact->value)
                ? json_decode($contact->value, true, flags: JSON_THROW_ON_ERROR)
                : [];

            $body->add('contact', json_encode(
                ['api_key' => $this->apiKey] + (is_array($fields) ? $fields : []),
                JSON_THROW_ON_ERROR
            ));
        } elseif ($body instanceof MergeableBody) {
            $body->merge(['api_key' => $this->apiKey]);
        }
    }
}
