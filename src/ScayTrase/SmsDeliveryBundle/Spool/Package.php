<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-08-06
 * Time: 22:42
 */

namespace ScayTrase\SmsDeliveryBundle\Spool;

use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;
use ScayTrase\SmsDeliveryBundle\Transport\TransportInterface;

class Package
{
    const STATUS_FAIL = 'fail';
    const STATUS_SUCCESS = 'success';
    const STATUS_SPOOLED = 'spooled';
    const STATUS_DISABLED = 'disabled';

    const REASON_OK = 'Ok';

    /** @var  TransportInterface */
    private $transport;
    /** @var  ShortMessageInterface */
    private $message;
    /** @var  string */
    private $status;
    /** @var  string */
    private $reason;

    /**
     * Package constructor.
     * @param TransportInterface $transport
     * @param ShortMessageInterface $message
     */
    public function __construct(TransportInterface $transport, ShortMessageInterface $message)
    {
        $this->transport = $transport;
        $this->message = $message;
        $this->status = self::STATUS_SPOOLED;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return ShortMessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
    
    function __sleep()
    {
        return array('reason', 'status', 'message');
    }
}
