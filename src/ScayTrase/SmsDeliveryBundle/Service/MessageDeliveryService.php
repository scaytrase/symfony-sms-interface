<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.07.2014
 * Time: 14:15
 */

namespace ScayTrase\SmsDeliveryBundle\Service;

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
    /** @var  array[] */
    private $profile = array();

    /**
     * @param TransportInterface $transport
     * @param bool $deliveryDisabled
     * @param null|string $recipientOverride
     */
    public function __construct(
        TransportInterface $transport,
        $deliveryDisabled = false,
        $recipientOverride = null
    )
    {
        $this->transport = $transport;
        $this->deliveryDisabled = $deliveryDisabled;
        $this->recipientOverride = $recipientOverride;
    }

    /**
     * @param ShortMessageInterface $message
     * @return boolean True if delivery was success or disabled via config
     */
    public function send(ShortMessageInterface $message)
    {
        if ($this->deliveryDisabled === true) {
            $this->profile[] = array(
                'transport' => '/dev/null',
                'message_class' => get_class($message),
                'message' => $message,
                'status' => 'disabled',
                'reason' => 'sms_delivery.disable_delivery is true',
            );

            return true;
        }

        if (($this->recipientOverride) !== null) {
            $message->setRecipient($this->recipientOverride);
        }

        try {
            $result = $this->transport->send($message);

            $this->profile[] = array(
                'transport' => get_class($this->transport),
                'message_class' => get_class($message),
                'message' => $message,
                'status' => (true === $result) ? 'success' : 'fail',
                'reason' => (true === $result) ? 'OK' : $result
            );

            return $result;
        } catch (DeliveryFailedException $e) {
            $this->profile[] = array(
                'transport' => get_class($this->transport),
                'message_class' => get_class($message),
                'message' => $message,
                'status' => 'fail',
                'reason' => $e->getMessage(),
            );

            return false;
        }
    }

    /**
     * @return array[]
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
