<?php

namespace ScayTrase\SmsDeliveryBundle\Entity;

use ScayTrase\SmsDeliveryBundle\ShortMessage;

final class RecipientOverridingMessage implements ShortMessage
{
    /** @var ShortMessage */
    private $message;
    /** @var string */
    private $recipient;

    /**
     * RecipientOverridingMessage constructor.
     *
     * @param ShortMessage $message
     * @param string       $recipient
     */
    public function __construct(ShortMessage $message, $recipient)
    {
        $this->message   = $message;
        $this->recipient = $recipient;
    }

    /** {@inheritdoc} */
    public function getBody()
    {
        return $this->message->getBody();
    }

    /** {@inheritdoc} */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /** {@inheritdoc} */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }
}
