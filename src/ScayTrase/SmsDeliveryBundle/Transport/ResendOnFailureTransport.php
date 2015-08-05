<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-08-05
 * Time: 22:13
 */

namespace ScayTrase\SmsDeliveryBundle\Transport;

use Psr\Log\LoggerInterface;
use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

class ResendOnFailureTransport implements TransportInterface
{
    const DEFAULT_DELAY = 15;

    const RETRY_COUNT = 3;

    /** @var  TransportInterface */
    private $transport;
    /** @var  int */
    private $delay = self::DEFAULT_DELAY;
    /** @var int */
    private $retryCount = self::RETRY_COUNT;
    /** @var  LoggerInterface|null */
    private $logger;

    /**
     * ResendOnFailureTransport constructor.
     * @param TransportInterface $transport
     * @param int $delay
     * @param int $retryCount
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportInterface $transport,
        $delay = self::DEFAULT_DELAY,
        $retryCount = self::RETRY_COUNT,
        LoggerInterface $logger = null
    )
    {
        $this->transport = $transport;
        $this->delay = $delay;
        $this->retryCount = $retryCount;
        $this->logger = $logger;
    }

    /**
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param TransportInterface $transport
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param int $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @param ShortMessageInterface $message
     * @return boolean
     *
     * @throws DeliveryFailedException
     */
    public function send(ShortMessageInterface $message)
    {
        for ($i = 0; $i < $this->retryCount; $i++) {
            $result = false;
            try {
                $result = $this->transport->send($message);
            } catch (DeliveryFailedException $exception) {
                if ($this->logger) {
                    $this->logger->warning($exception->getMessage());
                }
            }

            if ($result === true) {
                return true;
            }

            sleep($this->delay);
        }

        if ($this->logger) {
            $this->logger->error('Delivery try count exceeded');
        }

        return false;
    }
}
