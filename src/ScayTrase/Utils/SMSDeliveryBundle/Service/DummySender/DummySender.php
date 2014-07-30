<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.06.2014
 * Time: 13:10
 */

namespace ScayTrase\Utils\SMSDeliveryBundle\Service;


use ScayTrase\Utils\SMSDeliveryBundle\Exception\DeliveryFailedException;

class DummySender extends MessageDeliveryService{

    /**
     * @param ShortMessageInterface $message
     * @return bool
     * @throws DeliveryFailedException
     */
    protected function sendMessage(ShortMessageInterface $message)
    {
        return false;
    }
}