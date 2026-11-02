<?php

namespace Okolaa\TermiiPHP\Webhooks;

use InvalidArgumentException;
use JsonException;
use Okolaa\TermiiPHP\Webhooks\Events\DeliveryReport;
use Okolaa\TermiiPHP\Webhooks\Events\DeviceStatus;
use Okolaa\TermiiPHP\Webhooks\Events\InboundMessage;

/**
 * Parses a Termii webhook body into a typed event.
 *
 * Verify the signature with WebhookSignature::isValid() before trusting the result.
 */
class WebhookEvent
{
    /**
     * @throws InvalidArgumentException when the body is not a JSON object
     */
    public static function fromPayload(string $payload): DeliveryReport|InboundMessage|DeviceStatus
    {
        try {
            $data = json_decode($payload, true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException('Webhook payload is not valid JSON.', previous: $e);
        }

        if (! is_array($data)) {
            throw new InvalidArgumentException('Webhook payload must be a JSON object.');
        }

        return self::fromArray($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): DeliveryReport|InboundMessage|DeviceStatus
    {
        return match (true) {
            ($data['type'] ?? null) === 'device_status' || array_key_exists('device_id', $data) => DeviceStatus::fromArray($data),
            array_key_exists('received_at', $data)                                              => InboundMessage::fromArray($data),
            default                                                                             => DeliveryReport::fromArray($data),
        };
    }
}
