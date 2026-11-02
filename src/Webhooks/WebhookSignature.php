<?php

namespace Okolaa\TermiiPHP\Webhooks;

/**
 * Verifies that a webhook request came from Termii.
 *
 * Termii sends an X-Termii-Signature header holding an HMAC-SHA512 of the raw
 * request body, signed with your account's secret key.
 *
 * @see https://developers.termii.com/events-and-reports
 */
class WebhookSignature
{
    public const HEADER = 'X-Termii-Signature';

    /**
     * @param string      $payload   The raw request body, exactly as received (not re-encoded JSON)
     * @param string|null $signature The X-Termii-Signature header value
     * @param string      $secret    Your Termii secret key
     */
    public static function isValid(
        string $payload,
        ?string $signature,
        #[\SensitiveParameter]
        string $secret
    ): bool {
        if ($signature === null || $signature === '' || $secret === '') {
            return false;
        }

        $expected = hash_hmac('sha512', $payload, $secret);

        return hash_equals($expected, strtolower(trim($signature)));
    }
}
