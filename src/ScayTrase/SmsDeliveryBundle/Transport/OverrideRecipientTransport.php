<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 13.08.2015
 * Time: 8:25
 */

namespace ScayTrase\SmsDeliveryBundle\Transport;

use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

class OverrideRecipientTransport implements TransportInterface
{
    /** @var  TransportInterface */
    private $transport;

    /** @var  string */
    private $recipient;

    /**
     * OverrideRecipientTransport constructor.
     * @param TransportInterface $transport
     * @param string $recipient
     */
    public function __construct(TransportInterface $transport, $recipient)
    {
        $this->transport = $transport;
        $this->recipient = $recipient;
    }

    /**
     * @param ShortMessageInterface $message
     * @return boolean
     *
     * @throws DeliveryFailedException
     */
    public function send(ShortMessageInterface $message)
    {
        $message->setRecipient($this->recipient);
        return $this->transport->send($message);
    }
}
