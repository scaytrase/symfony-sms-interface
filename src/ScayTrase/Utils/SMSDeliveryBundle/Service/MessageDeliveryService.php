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

/**
 * Class MessageDeliveryService
 * @package ScayTrase\Utils\SMSDeliveryBundle\Service
 * Basic sender service class. Implement sendMessage() method with actual 3rd-party SMS sending API interaction.
 */
abstract class MessageDeliveryService
{
    /** @var  ContainerInterface */
    private $container;
    /** @var string Last delivery reason */
    private $last_reason = null;
    /**
     * @var array
     * Array of delivery records with message,status and reason fields
     * Message field implements ShortMessageInterface
     */
    private $message_collector = array();

    /**
     * @param ContainerInterface $container
     * Default construcitr
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     * @return mixed
     * Return all data that collector wants
     */
    public function getCollectorData()
    {
        return array(
            'messages' => $this->message_collector,
            'service' => get_class($this),
        );
    }

    /**
     * @param ShortMessageInterface $message
     * @return boolean True if delivery was success or disabled via config
     */
    public function send(ShortMessageInterface $message)
    {
        $this->last_reason = null;
        if ($this->container->getParameter('sms_delivery.disable_delivery') === true) {
            $this->message_collector[] = array(
                'message' => $message,
                'status' => 'disabled',
                'reason' => 'sms_delivery.disable_delivery is true',
            );
            return true;
        }

        if (($recipient = $this->container->getParameter('sms_delivery.delivery_recipient')) !== null) {
            $message->setRecipient($recipient);
        }

        try {
            $result = $this->sendMessage($message);

            $this->message_collector[] = array(
                'message' => $message,
                'status' => $result ? 'success' : 'fail',
                'reason' => $this->getLastReason()
            );

            return $result;
        } catch (DeliveryFailedException $e) {

            $this->message_collector[] = array(
                'message' => $message,
                'status' => 'fail',
                'reason' => $e->getMessage().PHP_EOL.$this->getLastReason(),
            );

            return false;
        }
    }

    /**
     * @param ShortMessageInterface $message
     * @return bool
     * @throws DeliveryFailedException
     * Actual sending message. Implementation depends on used service.
     */
    protected abstract function sendMessage(ShortMessageInterface $message);

    /**
     * @return mixed
     * Return last delivery reason
     */
    private  function getLastReason()
    {
        return $this->last_reason;
    }

    /**
     * @param mixed $last_reason
     * Sets last delivery reason
     */
    protected function setLastReason($last_reason)
    {
        $this->last_reason = $last_reason;
    }
} 