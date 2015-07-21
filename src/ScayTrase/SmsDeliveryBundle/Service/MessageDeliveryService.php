<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 14:15
 */

namespace ScayTrase\SmsDeliveryBundle\Service;

use ScayTrase\SmsDeliveryBundle\DataCollector\MessageDeliveryDataCollector;
use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;
use ScayTrase\SmsDeliveryBundle\Transport\TransportInterface;

/**
 * Class MessageDeliveryService
 * @package ScayTrase\Utils\SMSDeliveryBundle\Service
 * Basic sender service class. Implement sendMessage() method with actual 3rd-party SMS sending API interaction.
 */
class MessageDeliveryService
{
    /** @var  TransportInterface */
    protected $transport;
    /** @var  boolean */
    private $deliveryDisabled;
    /** @var  string */
    private $recipientOverride;
    /** @var  MessageDeliveryDataCollector|null */
    private $dataCollector;

    /**
     * @param TransportInterface $transport
     * @param bool $deliveryDisabled
     * @param null|string $recipientOverride
     * @param MessageDeliveryDataCollector $collector
     */
    public function __construct(
        TransportInterface $transport,
        $deliveryDisabled = false,
        $recipientOverride = null,
        MessageDeliveryDataCollector $collector = null
    ) {
        $this->transport = $transport;
        $this->deliveryDisabled = $deliveryDisabled;
        $this->recipientOverride = $recipientOverride;
        $this->dataCollector = $collector;
    }

    /**
     * @param ShortMessageInterface $message
     * @return boolean True if delivery was success or disabled via config
     */
    public function send(ShortMessageInterface $message)
    {
        if ($this->deliveryDisabled === true) {
            if ($this->dataCollector) {
                $this->dataCollector->putMessageInfo(array(
                    'transport' => null,
                    'message' => $message,
                    'status' => 'disabled',
                    'reason' => 'sms_delivery.disable_delivery is true',
                ));
            }

            return true;
        }

        if (($this->recipientOverride) !== null) {
            $message->setRecipient($this->recipientOverride);
        }

        try {
            $result = $this->transport->send($message);

            if ($this->dataCollector) {
                $this->dataCollector->putMessageInfo(array(
                    'transport' => get_class($this->transport),
                    'message' => $message,
                    'status' => (true === $result) ? 'success' : 'fail',
                    'reason' => (true === $result) ? 'OK' : $result
                ));
            }

            return $result;
        } catch (DeliveryFailedException $e) {
            if ($this->dataCollector) {
                $this->dataCollector->putMessageInfo(array(
                    'transport' => get_class($this->transport),
                    'message' => $message,
                    'status' => 'fail',
                    'reason' => $e->getMessage(),
                ));
            }

            return false;
        }
    }
}
