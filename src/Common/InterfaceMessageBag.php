<?php

namespace huenisys\Utils\Common;

interface InterfaceMessageBag {

    /**
     * Returns message corresponding to key
     *
     * @param string $messageKey
     * @return string get value of key
     */
    function getMessage(string $messageKey);

    /**
     * Returns the message bag instance
     *
     * @return huenisys\Utils\Common\InterfaceMessageBag
     */
    function getMessageBag();

    /**
     * Returns the current message array
     *
     * @return array
     */
    function getMessages();

    /**
     * Resets the message bag
     *
     * @param array $messagesArray
     * @return array returns the message bag that was set
     */
    function setMessageBag(?array $messagesArray);

}
