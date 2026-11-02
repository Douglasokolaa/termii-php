<?php

namespace Okolaa\TermiiPHP;

use InvalidArgumentException;
use Okolaa\TermiiPHP\Resources\Campaign\CampaignResource;
use Okolaa\TermiiPHP\Resources\InsightResource;
use Okolaa\TermiiPHP\Resources\MessagingResource;
use Okolaa\TermiiPHP\Resources\SenderIdResource;
use Okolaa\TermiiPHP\Resources\TokenResource;
use RuntimeException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Connector;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Traits\Plugins\HasTimeout;
use Throwable;

class TermiiConnector extends Connector
{
    use HasTimeout;
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    protected int $connectTimeout = 30;

    protected int $requestTimeout = 60;

    protected SenderIdResource $senderIdResource;

    protected MessagingResource $messagingResource;

    protected CampaignResource $campaignResource;

    protected TokenResource $tokenResource;

    private InsightResource $insightResource;

    public function __construct(
        #[\SensitiveParameter]
        protected readonly string $apiKey,
        protected readonly string $baseUrl
    ) {
        if ($apiKey === '') {
            throw new InvalidArgumentException('The Termii API key must not be empty.');
        }

        if (parse_url($baseUrl, PHP_URL_SCHEME) !== 'https' || parse_url($baseUrl, PHP_URL_HOST) === null) {
            throw new InvalidArgumentException('The Termii base URL must be an absolute https:// URL.');
        }
    }

    /**
     * Re-throws transport failures with the API key removed from the message, since
     * GET and DELETE requests carry it in the query string and the HTTP client echoes the URL.
     */
    public function send(Request $request, ?MockClient $mockClient = null, ?callable $handleRetry = null): Response
    {
        try {
            return parent::send($request, $mockClient, $handleRetry);
        } catch (FatalRequestException $exception) {
            // The original exception is not chained: its message may hold the full URL with the key.
            throw new FatalRequestException(
                new RuntimeException($this->redact($exception->getMessage()), (int) $exception->getCode()),
                $exception->getPendingRequest()
            );
        }
    }

    private function redact(string $message): string
    {
        $message = preg_replace('/(api_key=)[^&\s"\']+/i', '$1[redacted]', $message) ?? '';

        return str_replace([$this->apiKey, rawurlencode($this->apiKey), urlencode($this->apiKey)], '[redacted]', $message);
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'apiKey'  => '[redacted]',
            'baseUrl' => $this->baseUrl,
        ];
    }

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function senderIdApi(): SenderIdResource
    {
        return $this->senderIdResource ??= new SenderIdResource($this);
    }

    public function messagingApi(): MessagingResource
    {
        return $this->messagingResource ??= new MessagingResource($this);
    }

    public function campaignApi(): CampaignResource
    {
        return $this->campaignResource ??= new CampaignResource($this);
    }

    public function tokenApi(): TokenResource
    {
        return $this->tokenResource ??= new TokenResource($this);
    }

    public function insightApi(): InsightResource
    {
        return $this->insightResource ??= new InsightResource($this);
    }

    protected function defaultAuth(): TermiiAuthenticator
    {
        return new TermiiAuthenticator($this->apiKey);
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    protected function defaultConfig(): array
    {
        return [
            'verify' => true,
        ];
    }

    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return new RequestException($response, previous: $senderException);
    }
}
