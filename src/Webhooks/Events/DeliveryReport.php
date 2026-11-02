<?php

namespace Okolaa\TermiiPHP\Webhooks\Events;

use Okolaa\TermiiPHP\Data\Contracts\ConvertsArrayToDTO;

/**
 * Delivery status of an outbound message.
 */
class DeliveryReport implements ConvertsArrayToDTO
{
    public function __construct(
        public readonly ?string $type,
        public readonly ?string $id,
        public readonly ?string $messageId,
        public readonly ?string $receiver,
        public readonly ?string $sender,
        public readonly ?string $message,
        public readonly ?string $sentAt,
        public readonly ?string $cost,
        public readonly ?string $status,
        public readonly ?string $channel,
    ) {
    }

    public static function fromArray(array $data): DeliveryReport
    {
        return new self(
            type: Field::string($data, 'type'),
            id: Field::string($data, 'id'),
            messageId: Field::string($data, 'message_id'),
            receiver: Field::string($data, 'receiver'),
            sender: Field::string($data, 'sender'),
            message: Field::string($data, 'message'),
            sentAt: Field::string($data, 'sent_at'),
            cost: Field::string($data, 'cost'),
            status: Field::string($data, 'status'),
            channel: Field::string($data, 'channel'),
        );
    }
}
