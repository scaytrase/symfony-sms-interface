<?php

namespace ScayTrase\SmsDeliveryBundle;

use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

interface ShortMessage extends ShortMessageInterface
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
}
