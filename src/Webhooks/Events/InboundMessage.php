<?php

namespace Okolaa\TermiiPHP\Webhooks\Events;

use Okolaa\TermiiPHP\Data\Contracts\ConvertsArrayToDTO;

/**
 * A message a customer sent to one of your numbers or devices.
 */
class InboundMessage implements ConvertsArrayToDTO
{
    public function __construct(
        public readonly ?string $type,
        public readonly ?string $id,
        public readonly ?string $messageId,
        public readonly ?string $receiver,
        public readonly ?string $sender,
        public readonly ?string $message,
        public readonly ?string $receivedAt,
        public readonly ?string $cost,
        public readonly ?string $status,
        public readonly ?string $channel,
    ) {
    }

    public static function fromArray(array $data): InboundMessage
    {
        return new self(
            type: Field::string($data, 'type'),
            id: Field::string($data, 'id'),
            messageId: Field::string($data, 'message_id'),
            receiver: Field::string($data, 'receiver'),
            sender: Field::string($data, 'sender'),
            message: Field::string($data, 'message'),
            receivedAt: Field::string($data, 'received_at'),
            cost: Field::string($data, 'cost'),
            status: Field::string($data, 'status'),
            channel: Field::string($data, 'channel'),
        );
    }
}
