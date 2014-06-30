<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.06.2014
 * Time: 12:49
 */

namespace ScayTrase\Utils\SMSDeliveryBundle\Service;


use ScayTrase\Utils\SMSDeliveryBundle\Entity\ShortMessage;

interface MessageDeliveryInterface
{
    /**
     * Actually send message thru the messenger
     * @param ShortMessage $message
     * @return bool True on success
     * @throws DeliveryFailedException
     */
    public function sendMessage(ShortMessage $message);
} 