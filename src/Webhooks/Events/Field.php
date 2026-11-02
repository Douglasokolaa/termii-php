<?php

namespace Okolaa\TermiiPHP\Webhooks\Events;

/**
 * @internal
 */
final class Field
{
    /**
     * @param array<string, mixed> $data
     */
    public static function string(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_scalar($value) ? (string) $value : null;
    }
}
