<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 06.08.2015
 * Time: 17:49
 */

namespace ScayTrase\SmsDeliveryBundle\Spool;

use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;
use ScayTrase\SmsDeliveryBundle\Transport\TransportInterface;

class InstantSpool implements SpoolInterface
{
    /**
     * @param TransportInterface $transport
     * @param ShortMessageInterface $message
     *
     * @return bool
     */
    public function addMessage(TransportInterface $transport, ShortMessageInterface $message)
    {
        return $transport->send($message);
    }

    public function clear()
    {
    }

    public function flush()
    {
    }
}
