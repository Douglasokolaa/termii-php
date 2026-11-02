<?php

namespace Okolaa\TermiiPHP\Enums;

enum MessageChannel: string
{
    /** This channel sends messages via WhatsApp */
    case WhatsApp = 'WhatsApp';

    /** This channel is used to send promotional messages and messages to phone number not on dnd */
    case Generic = 'generic';

    /** On this channel all your messages deliver whether there is dnd restriction or not on the phone number */
    case DND = 'dnd';

    /** Converts the text to speech and delivers it as a voice call */
    case Voice = 'voice';
}
