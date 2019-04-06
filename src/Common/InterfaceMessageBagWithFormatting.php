<?php

namespace huenisys\Utils\Common;

interface InterfaceMessageBagWithFormatting
extends InterfaceMessageBag {

    /**
     * Return
     *
     * @param string $messageKey The array key
     * @param array:null $fillerValues will be unshifted with format at index 0
     * @return string Formatted string from sprintf with unpacked $values as param
     */
    function getFormattedMessage(string $messageKey, ?array $fillerValues);
}
