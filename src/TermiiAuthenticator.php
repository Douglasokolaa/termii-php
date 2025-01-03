<?php

namespace Okolaa\TermiiPHP;

use Saloon\Contracts\Authenticator;
use Saloon\Contracts\Body\BodyRepository;
use Saloon\Contracts\Body\MergeableBody;
use Saloon\Enums\Method;
use Saloon\Http\PendingRequest;
use Saloon\Repositories\Body\MultipartBodyRepository;

class TermiiAuthenticator implements Authenticator
{
    public function __construct(protected readonly string $apiKey)
    {
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
            default => false,
        };
    }

    /**
     * Merge the API key into the request body if the body is mergeable.
     */
    private function mergeApiKeyIntoBody(BodyRepository $body): void
    {
        if ($body instanceof MultipartBodyRepository) {
            $body->add(
                'contact',
                json_encode(
                    json_decode($body->get('contact')->value, true)
                    + [
                        'api_key' => $this->apiKey
                    ]
                )
            );
        } elseif ($body instanceof MergeableBody) {
            $body->merge(['api_key' => $this->apiKey]);
        }
    }
}
