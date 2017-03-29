<?php

namespace ScayTrase\SmsDeliveryBundle\Transport;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ScayTrase\SmsDeliveryBundle\Exception\DeliveryFailedException;
use ScayTrase\SmsDeliveryBundle\Service\ShortMessageInterface;

class ResendOnFailureTransport implements TransportInterface
{
    const DEFAULT_DELAY = 15;
    const RETRY_COUNT   = 3;

    /** @var  TransportInterface */
    private $transport;
    /** @var  int */
    private $delay;
    /** @var int */
    private $retryCount;
    /** @var  LoggerInterface|null */
    private $logger;

    /**
     * ResendOnFailureTransport constructor.
     *
     * @param TransportInterface $transport
     * @param int                $delay
     * @param int                $retryCount
     * @param LoggerInterface    $logger
     */
    public function __construct(
        TransportInterface $transport,
        $delay = self::DEFAULT_DELAY,
        $retryCount = self::RETRY_COUNT,
        LoggerInterface $logger = null
    ) {
        $this->transport  = $transport;
        $this->delay      = $delay;
        $this->retryCount = $retryCount;
        $this->logger     = $logger ?: new NullLogger();
    }

    /**
     * @param ShortMessageInterface $message
     *
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
                $this->logger->warning($exception->getMessage());
            }

            if ($result === true) {
                return true;
            }

            sleep($this->delay);
        }

        $this->logger->error('Delivery try count exceeded');

        return false;
    }
}
