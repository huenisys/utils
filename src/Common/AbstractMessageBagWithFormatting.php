<?php

namespace huenisys\Utils\Common;

use huenisys\Utils\MessageBag;

abstract class AbstractMessageBagWithFormatting
extends MessageBag
implements InterfaceMessageBagWithFormatting {

    protected static $defaultMessagesArray = [
        'hello' => 'Hello %s',
    ];

    /**
     * Return formatted string
     *
     * @param string $messageKey
     * @param array|null $fillerValues will be unshifted with format at index 0
     * @return string Formatted string from sprintf with unpacked $fillerValues as param
     */
    function getFormattedMessage(string $messageKey, ?array $fillerValues = [])
    {
        if (! is_null($fillerValues) && $fillerValues != []) :
            array_unshift($fillerValues, $this->getMessage($messageKey));
            return sprintf(...$fillerValues);
        else:
            return $this->getMessage($messageKey);
        endif;
    }
}
