<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.06.2014
 * Time: 12:46
 */

namespace ScayTrase\Utils\SMSDeliveryBundle\Entity;


use ScayTrase\Utils\SMSDeliveryBundle\Service\ShortMessageInterface;

class DummyMessage implements ShortMessageInterface
{

    private $recipient = null;
    private $body = "I'm a dummy message";
    /**
     * @return null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return null
     */
    public function getRecipient()
    {
        return $this->recipient;
    }


    /**
     * Set Message Recipient
     * @param $recipient string
     * @return void
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }
}