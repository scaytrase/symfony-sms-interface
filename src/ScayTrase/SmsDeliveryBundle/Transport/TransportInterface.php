<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.07.2015
 * Time: 15:50
 */

namespace ScayTrase\SmsDeliveryBundle\Transport;

use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

interface TransportInterface
{
    /**
     * @param ShortMessageInterface $message
     * @return boolean
     *
     * @throws DeliveryFailedException
     */
    public function send(ShortMessageInterface $message);
}
