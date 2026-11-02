<?php

namespace Okolaa\TermiiPHP;

class Termii
{
    private static ?TermiiConnector $connector = null;

    private static ?string $fingerprint = null;

    /**
     * Returns a shared connector, rebuilt whenever the API key or base URL changes so
     * a call for one account never returns a connector holding another account's key.
     */
    public static function initialize(
        #[\SensitiveParameter]
        string $apiKey,
        string $baseUrl = 'https://v3.api.termii.com'
    ): TermiiConnector {
        $fingerprint = hash('sha256', $apiKey . "\0" . $baseUrl);

        if (self::$connector === null || ! hash_equals((string) self::$fingerprint, $fingerprint)) {
            self::$connector   = new TermiiConnector($apiKey, $baseUrl);
            self::$fingerprint = $fingerprint;
        }

        return self::$connector;
    }
}
