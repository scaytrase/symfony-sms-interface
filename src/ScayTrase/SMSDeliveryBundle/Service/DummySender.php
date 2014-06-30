<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.06.2014
 * Time: 13:10
 */

namespace ScayTrase\Utils\SMSDeliveryBundle\Service;


use ScayTrase\Utils\SMSDeliveryBundle\Entity\ShortMessage;

class DummySender implements MessageDeliveryInterface{

    /**
     * Actually send message thru the messenger
     * @param ShortMessage $message
     * @return bool True on success
     * @throws DeliveryFailedException
     */
    public function sendMessage(ShortMessage $message)
    {
        return false;
    }
}