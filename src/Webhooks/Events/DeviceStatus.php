<?php

namespace Okolaa\TermiiPHP\Webhooks\Events;

use Okolaa\TermiiPHP\Data\Contracts\ConvertsArrayToDTO;

/**
 * A WhatsApp device connected or disconnected.
 */
class DeviceStatus implements ConvertsArrayToDTO
{
    public function __construct(
        public readonly ?string $type,
        public readonly ?string $status,
        public readonly ?string $deviceId,
        public readonly ?string $name,
    ) {
    }

    public static function fromArray(array $data): DeviceStatus
    {
        return new self(
            type: Field::string($data, 'type'),
            status: Field::string($data, 'status'),
            deviceId: Field::string($data, 'device_id'),
            name: Field::string($data, 'name'),
        );
    }
}
