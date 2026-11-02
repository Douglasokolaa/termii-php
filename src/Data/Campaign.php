<?php

namespace Okolaa\TermiiPHP\Data;

use Okolaa\TermiiPHP\Data\Contracts\ConvertsDtoToRequestArray;
use Okolaa\TermiiPHP\Enums\MessageChannel;

class Campaign implements Contracts\ConvertsArrayToDTO, ConvertsDtoToRequestArray
{
    public function __construct(
        public readonly string         $phonebookId,
        public readonly string         $senderId,
        public readonly ?MessageChannel $channel,
        public readonly string         $campaignType,
        public readonly ?string        $runAt = null,
        public readonly ?string        $status = null,
        public readonly ?string        $id = null,
        public readonly ?string        $countryCode = null,
        public readonly ?string        $message = null,
        public readonly ?string        $messageType = null,
        public readonly ?string        $totalRecipients = null,
        public readonly ?string        $delimiter = null,
        public readonly ?string        $removeDuplicate = 'yes',
        public readonly ?string        $createdAt = null,
    ) {
    }

    public static function fromArray(array $data): Campaign
    {
        return new self(
            phonebookId: $data['phone_book'],
            senderId: $data['sender'],
            channel: MessageChannel::tryFrom($data['channel']),
            campaignType: $data['camp_type'],
            runAt: $data['run_at'],
            status: $data['status'],
            id: $data['campaign_id'],
            totalRecipients: $data['total_recipients'],
            createdAt: $data['created_at'],
        );
    }

    public function toRequestArray(): array
    {
        return [
            'country_code'        => $this->countryCode,
            'sender_id'           => $this->senderId,
            'message'             => $this->message,
            'channel'             => $this->channel?->value,
            'message_type'        => $this->messageType,
            'phonebook_id'        => $this->phonebookId,
            'delimiter'           => $this->delimiter,
            'remove_duplicate'    => $this->removeDuplicate,
            'campaign_type'       => $this->campaignType,
            'schedule_time'       => $this->runAt,
            'schedule_sms_status' => $this->status,
        ];
    }
}
