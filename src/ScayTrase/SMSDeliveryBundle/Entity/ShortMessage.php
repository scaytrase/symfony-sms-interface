<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.06.2014
 * Time: 12:46
 */

namespace ScayTrase\Utils\SMSDeliveryBundle\Entity;


class ShortMessage
{
    private $id;
    private $recipient;
    private $body;

    function __construct($body = null, $recipient = null)
    {
        $this->body = $body;
        $this->recipient = $recipient;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param mixed $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
} 