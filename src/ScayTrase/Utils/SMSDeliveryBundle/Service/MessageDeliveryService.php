<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 14:15
 */

namespace ScayTrase\Utils\SMSDeliveryBundle\Service;


use ScayTrase\Utils\SMSDeliveryBundle\Exception\DeliveryFailedException;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class MessageDeliveryService
{
    /** @var  ContainerInterface */
    private $container;

    /** @var array succeeded messages list for profiling */
    private $messages_sent = array();
    /** @var array failed messages list for profiling */
    private $messages_failed = array();
    /** @var array disabled messages list for profiling */
    private $messages_disabled = array();

    public function getCollectorData()
    {
        return array(
            'messages_sent' => $this->messages_sent,
            'messages_failed' => $this->messages_failed,
            'messages_disabled' => $this->messages_disabled,
        );
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ShortMessageInterface $message
     * @return boolean true if delivery was success or disabled via config
     */
    public function send(ShortMessageInterface $message)
    {
        if ($this->container->getParameter('sms_delivery.disable_delivery') === true) {
            $this->messages_sent[] = $message;
            $this->messages_disabled[] = $message;
            return true;
        }

        if (($recipient = $this->container->getParameter('sms_delivery.delivery_recipient')) !== null) {
            $message->setRecipient($recipient);
        }

        $this->messages_sent[] = $message;
        try {
            $result = $this->sendMessage($message);

            if (!$result) $this->messages_failed[] = $message;
            return $result;
        } catch (DeliveryFailedException $e) {
            $this->messages_failed[] = $message;
            return false;
        }
    }

    /**
     * @param ShortMessageInterface $message
     * @return bool
     * @throws DeliveryFailedException
     */
    protected abstract function sendMessage(ShortMessageInterface $message);
} 