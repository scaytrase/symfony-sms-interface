<?php

namespace ScayTrase\SmsDeliveryBundle\Transport;

use ScayTrase\SmsDeliveryBundle\Entity\RecipientOverridingMessage;
use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

final class OverrideRecipientTransport implements TransportInterface
{
    /** @var  TransportInterface */
    private $transport;

    /** @var  string */
    private $recipient;

    /**
     * OverrideRecipientTransport constructor.
     *
     * @param TransportInterface $transport
     * @param string             $recipient
     */
    public function __construct(TransportInterface $transport, $recipient)
    {
        $this->transport = $transport;
        $this->recipient = $recipient;
    }

    /**
     * @param ShortMessageInterface $message
     *
     * @return boolean
     *
     * @throws DeliveryFailedException
     */
    public function send(ShortMessageInterface $message)
    {
        return $this->transport->send(new RecipientOverridingMessage($message, $this->recipient));
    }
}
