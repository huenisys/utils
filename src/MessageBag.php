<?php

namespace huenisys\Utils;

use huenisys\Utils\Common\InterfaceMessageBag;

class MessageBag implements InterfaceMessageBag
{
    private static $defaultMessagesArray = [
        'hello' => 'Hello World'
    ];

    /**
     * @var MessageBag The instance
     */
    protected $messageBag;

    /**
     * The message bag array
     *
     * @var array
     */
    protected $messagesArray;

    public function __construct(?array $messagesArray = null)
    {
        if (is_array($messagesArray)) :
            $this->setMessageBag($messagesArray);
        else:
            $this->setMessageBag(static::$defaultMessagesArray);
        endif;

        return $this->messageBag = $this;
    }

    public function getInstance()
    {
        return $this->messageBag;
    }

    public function getMessageBag()
    {
        return $this->getInstance();
    }

    public function getMessages()
    {
        return $this->messagesArray;
    }

    public function getMessage(string $messageKey)
    {
        return $this->getMessages()[$messageKey] ?? null;
    }

    public function setMessageBag(?array $messagesArray)
    {
        return $this->messagesArray = $messagesArray;
    }
}
