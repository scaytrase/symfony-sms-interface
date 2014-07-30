<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.06.2014
 * Time: 13:10
 */

namespace ScayTrase\Utils\SMSDeliveryBundle\Service\DummySender;


use ScayTrase\Utils\SMSDeliveryBundle\Exception\DeliveryFailedException;
use ScayTrase\Utils\SMSDeliveryBundle\Service\MessageDeliveryService;
use ScayTrase\Utils\SMSDeliveryBundle\Service\ShortMessageInterface;

class DummyMessageDeliveryService extends MessageDeliveryService{

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