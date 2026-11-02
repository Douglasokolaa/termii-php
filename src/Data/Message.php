<?php

namespace Okolaa\TermiiPHP\Data;

use Okolaa\TermiiPHP\Data\Contracts\ConvertsDtoToRequestArray;
use Okolaa\TermiiPHP\Enums\MessageChannel;

class Message implements ConvertsDtoToRequestArray
{
    /**
     * @param string|string[] $to
     * @param string          $from
     * @param string          $sms
     * @param MessageChannel  $channel
     * @param string          $type
     * @param string|null     $mediaUrl     The url to the file resource,
     * @param string|null     $mediaCaption The caption that should be added to the resource,
     */
    public function __construct(
        public readonly string|array   $to,
        public readonly string         $from,
        public readonly string         $sms,
        public readonly string         $type = 'sms',
        public readonly MessageChannel $channel = MessageChannel::Generic,
        public readonly ?string        $mediaUrl = '',
        public readonly ?string        $mediaCaption = '',
    ) {
    }

    public function toRequestArray(): array
    {
        $payload = [
            'to'      => $this->to,
            'from'    => $this->from,
            'sms'     => $this->sms,
            'type'    => $this->type,
            'channel' => $this->channel->value,
        ];

        // Termii only accepts media on the WhatsApp channel; omit it when no file is attached.
        if ($this->mediaUrl !== null && $this->mediaUrl !== '') {
            $payload['media'] = [
                'url'     => $this->mediaUrl,
                'caption' => $this->mediaCaption,
            ];
        }

        return $payload;
    }
}
