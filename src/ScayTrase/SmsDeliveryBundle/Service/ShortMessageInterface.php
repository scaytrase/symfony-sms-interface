<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 14:10
 */
namespace ScayTrase\SmsDeliveryBundle\Service;

interface ShortMessageInterface
{
    /**
     * Get Message Body
     * @return string message to be sent
     */
    public function getBody();

    /**
     * Get Message Recipient
     * @return string message recipient number
     */
    public function getRecipient();

    /**
     * Set Message Recipient
     * @param $recipient string
     * @return void
     */
    public function setRecipient($recipient);
}
