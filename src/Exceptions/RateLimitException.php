<?php

namespace Okolaa\TermiiPHP\Exceptions;

use Okolaa\TermiiPHP\RequestException;

/**
 * 429 Too Many Requests: slow down before retrying.
 */
class RateLimitException extends RequestException
{
    /**
     * Seconds to wait before retrying, when Termii sends a numeric Retry-After header.
     */
    public function retryAfter(): ?int
    {
        $value = $this->getResponse()->header('Retry-After');

        return is_string($value) && ctype_digit($value) ? (int) $value : null;
    }
}
